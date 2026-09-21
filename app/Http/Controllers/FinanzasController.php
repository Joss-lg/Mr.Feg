<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FlujoCaja;
use App\Models\PagoNomina;
use App\Models\Gasto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class FinanzasController extends Controller
{
    /**
     * Mostrar el dashboard de finanzas con métricas y flujo de caja
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'todos');
        if (!in_array($tab, ['todos', 'ingresos', 'egresos'], true)) {
            $tab = 'todos';
        }

        $empleados = User::where('esta_activo', true)
            ->orderBy('nombre')
            ->select(['id', 'nombre'])
            ->get();

        $now = Carbon::now();
        $mesActual = $now->month;
        $añoActual = $now->year;

        $ingresosMes = FlujoCaja::ingresos()->delMes($mesActual, $añoActual)->sum('monto');
        $egresosMes  = FlujoCaja::egresos()->sinCancelaciones()->delMes($mesActual, $añoActual)->sum('monto');
        $canceladoMes = FlujoCaja::egresos()->soloCancelaciones()->delMes($mesActual, $añoActual)->sum('monto');
        $balanceNeto = $ingresosMes - $egresosMes;

        $nominaPagada = PagoNomina::pagados()->porMes($mesActual, $añoActual)->sum('monto_neto');

        $query = FlujoCaja::query();

        match ($tab) {
            'ingresos' => $query->ingresos(),
            'egresos'  => $query->egresos()->sinCancelaciones(),
            default    => null,
        };

        $flujosCaja = $query->ordenado('desc')->paginate(20)->withQueryString();

        $categoriasIngresos = FlujoCaja::ingresos()
            ->delMes($mesActual, $añoActual)
            ->selectRaw('categoria, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('categoria')
            ->get();

        $categoriasEgresos = FlujoCaja::egresos()
            ->sinCancelaciones()
            ->delMes($mesActual, $añoActual)
            ->selectRaw('categoria, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('categoria')
            ->get();

        $ultimosSieteDias = FlujoCaja::entre($now->copy()->subDays(7)->startOfDay(), $now->endOfDay())
            ->sinCancelaciones()
            ->ordenado('desc')
            ->get();

        $top5Gastos = FlujoCaja::egresos()
            ->sinCancelaciones()
            ->delMes($mesActual, $añoActual)
            ->orderByDesc('monto')
            ->limit(5)
            ->get();

        $metodosPago = FlujoCaja::delMes($mesActual, $añoActual)
            ->selectRaw('metodo_pago, tipo, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('metodo_pago', 'tipo')
            ->get();

        return view('admin.finanzas.index', compact(
            'ingresosMes', 'egresosMes', 'balanceNeto', 'nominaPagada', 
            'flujosCaja', 'tab', 'categoriasIngresos', 'categoriasEgresos', 
            'ultimosSieteDias', 'top5Gastos', 'metodosPago', 'mesActual', 'añoActual', 'empleados'
        ));
    }

    /**
     * CORTE MENSUAL
     */
    public function corteMensual(Request $request): View
    {
        [$mes, $año] = $this->sanearMesAño($request);

        $inicioMes = Carbon::create($año, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();

        $movimientos = FlujoCaja::whereBetween('fecha', [$inicioMes, $finMes])
            ->sinCancelaciones()
            ->orderBy('fecha', 'asc')
            ->get();

        $porDia = $movimientos->groupBy(fn ($m) => $m->fecha->toDateString());

        $dias = collect();
        $balanceAcumulado = 0;

        for ($d = 1; $d <= $inicioMes->daysInMonth; $d++) {
            $fecha = Carbon::create($año, $mes, $d);
            $fila  = $this->armarFilaDia($fecha, $porDia, $balanceAcumulado);
            $dias->push($fila);
        }

        $totales = $this->calcularTotales($dias);

        $categoriasIngresos = $movimientos->where('tipo', 'ingreso')
            ->groupBy('categoria')
            ->map(fn ($grupo) => (object) [
                'total'    => (float) $grupo->sum('monto'),
                'cantidad' => $grupo->count(),
            ]);

        $categoriasEgresos = $movimientos->where('tipo', 'egreso')
            ->groupBy('categoria')
            ->map(fn ($grupo) => (object) [
                'total'    => (float) $grupo->sum('monto'),
                'cantidad' => $grupo->count(),
            ]);

        $primerAño = FlujoCaja::min('fecha');
        $primerAño = $primerAño ? Carbon::parse($primerAño)->year : now()->year;
        $añosDisponibles = range((int) now()->year, $primerAño);

        return view('admin.finanzas.corte-mensual', compact(
            'dias', 'totales', 'mes', 'año',
            'categoriasIngresos', 'categoriasEgresos',
            'añosDisponibles', 'inicioMes'
        ));
    }

    public function exportarCortePDF(Request $request)
    {
        [$mes, $año] = $this->sanearMesAño($request);

        $inicioMes = Carbon::create($año, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();

        $esMesActual = $inicioMes->isSameMonth(now(), true);
        $ultimoDia   = $esMesActual ? now()->day : $inicioMes->daysInMonth;
        $fechaCorte  = Carbon::create($año, $mes, $ultimoDia)->endOfDay();

        $movimientos = FlujoCaja::whereBetween('fecha', [$inicioMes, $fechaCorte])
            ->sinCancelaciones()
            ->orderBy('fecha', 'asc')
            ->get();

        $porDia = $movimientos->groupBy(fn ($m) => $m->fecha->toDateString());

        $dias = collect();
        $balanceAcumulado = 0;

        for ($d = 1; $d <= $ultimoDia; $d++) {
            $fecha = Carbon::create($año, $mes, $d);
            $fila  = $this->armarFilaDia($fecha, $porDia, $balanceAcumulado);
            $dias->push($fila);
        }

        $totales = $this->calcularTotales($dias);

        $categoriasIngresos = $movimientos->where('tipo', 'ingreso')
            ->groupBy('categoria')
            ->map(fn ($grupo) => (object) [
                'total'    => (float) $grupo->sum('monto'),
                'cantidad' => $grupo->count(),
            ]);

        $categoriasEgresos = $movimientos->where('tipo', 'egreso')
            ->groupBy('categoria')
            ->map(fn ($grupo) => (object) [
                'total'    => (float) $grupo->sum('monto'),
                'cantidad' => $grupo->count(),
            ]);

        $pdf = Pdf::loadView('admin.finanzas.corte-mensual-pdf', [
            'dias'               => $dias,
            'totales'            => $totales,
            'mes'                => $mes,
            'año'                => $año,
            'inicioMes'          => $inicioMes,
            'fechaCorte'         => $fechaCorte,
            'esParcial'          => $esMesActual && $ultimoDia < $inicioMes->daysInMonth,
            'categoriasIngresos' => $categoriasIngresos,
            'categoriasEgresos'  => $categoriasEgresos,
            'generadoEn'         => now(),
            'generadoPor'        => auth()->user()->nombre ?? 'Sistema',
        ])->setPaper('letter', 'portrait');

        $sufijo = $esMesActual ? '_al_' . $fechaCorte->format('d') : '';
        $filename = "corte_mensual_{$año}_" . str_pad($mes, 2, '0', STR_PAD_LEFT) . $sufijo . ".pdf";

        return $pdf->download($filename);
    }

    public function exportarCorteCSV(Request $request)
    {
        [$mes, $año] = $this->sanearMesAño($request);

        $inicioMes = Carbon::create($año, $mes, 1)->startOfMonth();
        $finMes    = $inicioMes->copy()->endOfMonth();

        $movimientos = FlujoCaja::whereBetween('fecha', [$inicioMes, $finMes])
            ->sinCancelaciones()
            ->orderBy('fecha', 'asc')
            ->get();

        $porDia = $movimientos->groupBy(fn ($m) => $m->fecha->toDateString());

        $filename = "corte_mensual_{$año}_" . str_pad($mes, 2, '0', STR_PAD_LEFT) . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($porDia, $inicioMes, $año, $mes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 

            fputcsv($file, ['CORTE MENSUAL', $inicioMes->translatedFormat('F Y')], ';');
            fputcsv($file, [], ';');
            fputcsv($file, ['Fecha', 'Ingresos', 'Gastos', 'Nómina', 'Total Egresos', 'Balance del Día', 'Balance Acumulado'], ';');

            $totIngresos = 0;
            $totGastos = 0;
            $totNomina = 0;
            $acumulado = 0;

            for ($d = 1; $d <= $inicioMes->daysInMonth; $d++) {
                $fecha = Carbon::create($año, $mes, $d);
                $movsDelDia = $porDia->get($fecha->toDateString(), collect());

                $ingresos = $movsDelDia->where('tipo', 'ingreso')->sum('monto');
                $nomina   = $movsDelDia->where('tipo', 'egreso')->where('categoria', 'Nómina')->sum('monto');
                $gastos   = $movsDelDia->where('tipo', 'egreso')->where('categoria', '!=', 'Nómina')->sum('monto');
                $egresos  = $gastos + $nomina;
                $balance  = $ingresos - $egresos;
                $acumulado += $balance;

                $totIngresos += $ingresos;
                $totGastos   += $gastos;
                $totNomina   += $nomina;

                fputcsv($file, [
                    $fecha->format('Y-m-d'),
                    number_format((float) $ingresos, 2, '.', ''),
                    number_format((float) $gastos, 2, '.', ''),
                    number_format((float) $nomina, 2, '.', ''),
                    number_format((float) $egresos, 2, '.', ''),
                    number_format((float) $balance, 2, '.', ''),
                    number_format((float) $acumulado, 2, '.', ''),
                ], ';');
            }

            fputcsv($file, [
                'TOTALES',
                number_format((float) $totIngresos, 2, '.', ''),
                number_format((float) $totGastos, 2, '.', ''),
                number_format((float) $totNomina, 2, '.', ''),
                number_format((float) ($totGastos + $totNomina), 2, '.', ''),
                number_format((float) ($totIngresos - $totGastos - $totNomina), 2, '.', ''),
                '',
            ], ';');

            fputcsv($file, [], ';');
            fputcsv($file, ['DETALLE DE MOVIMIENTOS'], ';');
            fputcsv($file, ['Fecha', 'Hora', 'Tipo', 'Categoría', 'Concepto', 'Monto', 'Método de Pago'], ';');

            foreach ($porDia as $movsDelDia) {
                foreach ($movsDelDia as $flujo) {
                    fputcsv($file, [
                        $flujo->fecha->format('Y-m-d'),
                        $flujo->fecha->format('H:i'),
                        $flujo->getTipoLegible(),
                        $flujo->categoria,
                        $flujo->concepto,
                        number_format((float) $flujo->monto, 2, '.', ''),
                        $flujo->metodo_pago,
                    ], ';');
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * CORTE SEMANAL / POR RANGO DE FECHAS
     */
    public function corteSemanal(Request $request): View
    {
        $fechaInicioStr = $request->query('fecha_inicio', now()->startOfWeek()->toDateString());
        $fechaFinStr    = $request->query('fecha_fin', now()->endOfWeek()->toDateString());

        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin    = Carbon::parse($fechaFinStr)->endOfDay();

        $movimientos = FlujoCaja::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinCancelaciones()
            ->orderBy('fecha', 'asc')
            ->get();

        $porDia = $movimientos->groupBy(fn ($m) => $m->fecha->toDateString());

        $dias = collect();
        $balanceAcumulado = 0;

        $periodo = \Carbon\CarbonPeriod::create($fechaInicio, $fechaFin);
        
        foreach ($periodo as $fecha) {
            $fila  = $this->armarFilaDia($fecha, $porDia, $balanceAcumulado);
            $dias->push($fila);
        }

        $totales = $this->calcularTotales($dias);

        $categoriasIngresos = $movimientos->where('tipo', 'ingreso')
            ->groupBy('categoria')
            ->map(fn ($grupo) => (object) [
                'total'    => (float) $grupo->sum('monto'),
                'cantidad' => $grupo->count(),
            ]);

        $categoriasEgresos = $movimientos->where('tipo', 'egreso')
            ->groupBy('categoria')
            ->map(fn ($grupo) => (object) [
                'total'    => (float) $grupo->sum('monto'),
                'cantidad' => $grupo->count(),
            ]);

        return view('admin.finanzas.corte-semanal', compact(
            'dias', 'totales', 'fechaInicioStr', 'fechaFinStr',
            'categoriasIngresos', 'categoriasEgresos'
        ))->with([
            'fechaInicio' => $fechaInicioStr,
            'fechaFin'    => $fechaFinStr
        ]);
    }

    /**
     * Exportar el corte semanal a PDF
     */
    public function exportarCorteSemanalPDF(Request $request)
    {
        $fechaInicioStr = $request->query('fecha_inicio', now()->startOfWeek()->toDateString());
        $fechaFinStr    = $request->query('fecha_fin', now()->endOfWeek()->toDateString());

        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin    = Carbon::parse($fechaFinStr)->endOfDay();

        $movimientos = FlujoCaja::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinCancelaciones()
            ->orderBy('fecha', 'asc')
            ->get();

        $porDia = $movimientos->groupBy(fn ($m) => $m->fecha->toDateString());

        $dias = collect();
        $balanceAcumulado = 0;

        $periodo = \Carbon\CarbonPeriod::create($fechaInicio, $fechaFin);
        foreach ($periodo as $fecha) {
            $fila  = $this->armarFilaDia($fecha, $porDia, $balanceAcumulado);
            $dias->push($fila);
        }

        $totales = $this->calcularTotales($dias);

        $pdf = Pdf::loadView('admin.finanzas.corte-semanal-pdf', [
            'dias'               => $dias,
            'totales'            => $totales,
            'fechaInicio'        => $fechaInicio,
            'fechaFin'           => $fechaFin,
            'generadoEn'         => now(),
            'generadoPor'        => auth()->user()->nombre ?? 'Sistema',
        ])->setPaper('letter', 'portrait');

        $filename = "corte_semanal_{$fechaInicioStr}_al_{$fechaFinStr}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Exportar el corte semanal a CSV
     */
    public function exportarCorteSemanalCSV(Request $request)
    {
        $fechaInicioStr = $request->query('fecha_inicio', now()->startOfWeek()->toDateString());
        $fechaFinStr    = $request->query('fecha_fin', now()->endOfWeek()->toDateString());

        $fechaInicio = Carbon::parse($fechaInicioStr)->startOfDay();
        $fechaFin    = Carbon::parse($fechaFinStr)->endOfDay();

        $movimientos = FlujoCaja::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sinCancelaciones()
            ->orderBy('fecha', 'asc')
            ->get();

        $porDia = $movimientos->groupBy(fn ($m) => $m->fecha->toDateString());

        $filename = "corte_semanal_{$fechaInicioStr}_al_{$fechaFinStr}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($porDia, $fechaInicio, $fechaFin, $fechaInicioStr, $fechaFinStr) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 

            fputcsv($file, ['CORTE SEMANAL', "Del $fechaInicioStr al $fechaFinStr"], ';');
            fputcsv($file, [], ';');
            fputcsv($file, ['Fecha', 'Ingresos', 'Gastos', 'Nómina', 'Total Egresos', 'Balance del Día', 'Balance Acumulado'], ';');

            $totIngresos = $totGastos = $totNomina = $acumulado = 0;

            $periodo = \Carbon\CarbonPeriod::create($fechaInicio, $fechaFin);
            foreach ($periodo as $fecha) {
                $movsDelDia = $porDia->get($fecha->toDateString(), collect());

                $ingresos = $movsDelDia->where('tipo', 'ingreso')->sum('monto');
                $nomina   = $movsDelDia->where('tipo', 'egreso')->where('categoria', 'Nómina')->sum('monto');
                $gastos   = $movsDelDia->where('tipo', 'egreso')->where('categoria', '!=', 'Nómina')->sum('monto');
                $egresos  = $gastos + $nomina;
                $balance  = $ingresos - $egresos;
                $acumulado += $balance;

                $totIngresos += $ingresos;
                $totGastos   += $gastos;
                $totNomina   += $nomina;

                fputcsv($file, [
                    $fecha->format('Y-m-d'),
                    number_format((float) $ingresos, 2, '.', ''),
                    number_format((float) $gastos, 2, '.', ''),
                    number_format((float) $nomina, 2, '.', ''),
                    number_format((float) $egresos, 2, '.', ''),
                    number_format((float) $balance, 2, '.', ''),
                    number_format((float) $acumulado, 2, '.', ''),
                ], ';');
            }

            fputcsv($file, [
                'TOTALES',
                number_format((float) $totIngresos, 2, '.', ''),
                number_format((float) $totGastos, 2, '.', ''),
                number_format((float) $totNomina, 2, '.', ''),
                number_format((float) ($totGastos + $totNomina), 2, '.', ''),
                number_format((float) ($totIngresos - $totGastos - $totNomina), 2, '.', ''),
                '',
            ], ';');

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Helper privado: sanear mes y año de la request
     */
    private function sanearMesAño(Request $request): array
    {
        $mes = (int) $request->query('mes', now()->month);
        $año = (int) $request->query('año', now()->year);

        if ($mes < 1 || $mes > 12) {
            $mes = now()->month;
        }
        if ($año < 2020 || $año > (int) now()->year + 1) {
            $año = now()->year;
        }

        return [$mes, $año];
    }

    /**
     * Helper privado: construir la fila-resumen de un día
     */
    private function armarFilaDia(Carbon $fecha, $porDia, &$balanceAcumulado): object
    {
        $movsDelDia = $porDia->get($fecha->toDateString(), collect());

        $ingresos = $movsDelDia->where('tipo', 'ingreso')->sum('monto');
        $nomina   = $movsDelDia->where('tipo', 'egreso')
                               ->where('categoria', 'Nómina')
                               ->sum('monto');
        $gastos   = $movsDelDia->where('tipo', 'egreso')
                               ->where('categoria', '!=', 'Nómina')
                               ->sum('monto');

        $egresos  = $gastos + $nomina;
        $balance  = $ingresos - $egresos;
        $balanceAcumulado += $balance;

        return (object) [
            'fecha'             => $fecha,
            'ingresos'          => (float) $ingresos,
            'gastos'            => (float) $gastos,
            'nomina'            => (float) $nomina,
            'egresos'           => (float) $egresos,
            'balance'           => (float) $balance,
            'balance_acumulado' => (float) $balanceAcumulado,
            'movimientos'       => $movsDelDia,
            'tiene_movimientos' => $movsDelDia->isNotEmpty(),
        ];
    }

    /**
     * Helper privado: totales a partir de la colección de días
     */
    private function calcularTotales($dias): object
    {
        return (object) [
            'ingresos' => (float) $dias->sum('ingresos'),
            'gastos'   => (float) $dias->sum('gastos'),
            'nomina'   => (float) $dias->sum('nomina'),
            'egresos'  => (float) $dias->sum('egresos'),
            'balance'  => (float) $dias->sum('balance'),
        ];
    }

    public function exportarCSV(Request $request)
    {
        $mes = $request->query('mes', now()->month);
        $año = $request->query('año', now()->year);

        $flujos = FlujoCaja::delMes($mes, $año)->ordenado('desc')->get();
        $filename = "flujo_caja_{$año}_{$mes}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($flujos) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Fecha', 'Tipo', 'Categoría', 'Concepto', 'Monto', 'Método de Pago'], ';');

            foreach ($flujos as $flujo) {
                fputcsv($file, [
                    $flujo->fecha ? $flujo->fecha->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    $flujo->getTipoLegible(),
                    $flujo->categoria,
                    $flujo->concepto,
                    $flujo->monto,
                    $flujo->metodo_pago,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function estadisticasPeriodo(Request $request)
    {
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin'    => 'required|date|after_or_equal:fechaInicio',
        ]);

        $ingresos = FlujoCaja::ingresos()->entre($request->fechaInicio, $request->fechaFin)->sum('monto');
        $egresos  = FlujoCaja::egresos()->sinCancelaciones()->entre($request->fechaInicio, $request->fechaFin)->sum('monto');

        return response()->json([
            'ingresos' => (float)$ingresos,
            'egresos'  => (float)$egresos,
            'balance'  => (float)($ingresos - $egresos),
        ]);
    }

    public function guardarGasto(Request $request)
    {
        $request->validate([
            'concepto'    => 'required|string|max:255',
            'categoria'   => 'required|in:Compra Insumos,Servicios,Renta,Mantenimiento,Otro',
            'monto'       => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:Efectivo,Tarjeta,Transferencia',
            'estado'      => 'required|in:pendiente,pagado',
            'documento'   => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
        ]);

        Gasto::create([
            'concepto'    => $request->concepto,
            'categoria'   => $request->categoria,
            'monto'       => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'estado'      => $request->estado,
            'fecha'       => now(),
            'documento'   => $request->documento,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.finanzas.index')->with('success', 'Gasto registrado correctamente.');
    }

    public function guardarNomina(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'periodo'       => 'required|string|max:100',
            'sueldo_base'   => 'required|numeric|min:0',
            'bonos'         => 'nullable|numeric|min:0',
            'deducciones'   => 'nullable|numeric|min:0',
            'metodo_pago'   => 'required|in:Efectivo,Tarjeta,Transferencia',
            'estado'        => 'required|in:pendiente,pagado',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $sueldoBase  = $request->sueldo_base;
        $bonos       = $request->bonos ?? 0;
        $deducciones = $request->deducciones ?? 0;
        $montoNeto   = PagoNomina::calcularMontoNeto($sueldoBase, $bonos, $deducciones);

        PagoNomina::create([
            'user_id'       => $request->user_id,
            'periodo'       => $request->periodo,
            'sueldo_base'   => $request->sueldo_base,
            'bonos'         => $request->bonos ?? 0,
            'deducciones'   => $request->deducciones ?? 0,
            'monto_neto'    => $montoNeto,
            'metodo_pago'   => $request->metodo_pago,
            'estado'        => $request->estado,
            'fecha_pago'    => $request->estado === 'pagado' ? now() : null,
            'observaciones' => $request->observaciones,
        ]);

        return redirect()->route('admin.finanzas.index')->with('success', 'Pago de nómina registrado correctamente.');
    }
}