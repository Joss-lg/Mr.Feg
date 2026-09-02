<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pre-cuenta — Mesa {{ $mesa->numero }}</title>
<style>
    @page { size: 80mm auto; margin: 0; }

    body {
        width: 72mm;
        margin: 4mm auto;
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 13px;
        color: #000;
        text-transform: uppercase;
    }

    .text-center { text-align: center; }
    .font-bold { font-weight: bold; }
    .text-xl { font-size: 22px; letter-spacing: 1px; }
    .text-lg { font-size: 16px; }
    .mt-1 { margin-top: 5px; }
    .mb-1 { margin-bottom: 5px; }
    .py-1 { padding: 5px 0; }

    .dashed-line { border-top: 1px dashed #000; margin: 5px 0; }

    table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    th, td { text-align: left; vertical-align: top; padding: 3px 0; }
    th { border-bottom: 1px dashed #000; font-weight: bold; padding-bottom: 3px; font-size: 13px; }

    .item-principal { font-size: 15px; font-weight: 900; line-height: 1.2; }
    .sub-item { font-size: 12px; font-weight: normal; color: #444; line-height: 1.2; }

    .ticket-logo {
        width: 140px;
        height: auto;
        margin: 0 auto 2px auto;
        display: block;
        filter: grayscale(100%) contrast(1.2);
    }

    @media print {
        .no-print { display: none !important; }
        body { margin: 0 auto; }
    }
</style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="text-center mb-1">
        <img src="{{ asset('images/mrlogo.png') }}" alt="Mr. Feg" class="ticket-logo">
        <div style="font-size: 12px; margin-top: 2px;">PRE-CUENTA</div>
        <div style="font-size: 11px;">NO ES UN COMPROBANTE FISCAL</div>
        <div style="font-size: 12px; margin-top: 2px;">{{ $fecha->format('d/m/Y H:i') }}</div>
        @if($orden->mesero ?? false)
            <div style="font-size: 12px;">ATENDIÓ: {{ strtoupper($orden->mesero->nombre) }}</div>
        @endif

        <div class="font-bold text-lg mt-1 mb-1 py-1" style="border-top: 1px dashed #000; border-bottom: 1px dashed #000;">
            MESA {{ strtoupper($mesa->numero) }}
        </div>
    </div>

    {{-- Productos --}}
    @if($detalles->isEmpty())
        <div class="text-center" style="font-size: 12px; margin: 8px 0;">SIN PRODUCTOS ENVIADOS A COCINA</div>
    @else
        <table class="mb-1">
            <thead>
                <tr>
                    <th style="width: 75%; padding-left: 2px;">DESCRIPCIÓN</th>
                    <th style="width: 25%; text-align: right;">IMPORTE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detalles as $detalle)
                    @php
                        $precioLinea = ($detalle->precio_unitario ?? 0) * $detalle->cantidad;
                    @endphp
                    <tr class="item-principal">
                        <td style="padding-top: 6px; padding-left: 2px;">
                            {{ $detalle->cantidad }}X {{ strtoupper($detalle->producto->nombre ?? 'PRODUCTO') }}
                            @if($detalle->gramaje)
                                @php $gramajeLimpio = rtrim(rtrim(number_format((float) $detalle->gramaje, 2, '.', ''), '0'), '.'); @endphp
                                <div class="sub-item">{{ $gramajeLimpio }}G</div>
                            @endif
                            @if($detalle->notas)
                                <div class="sub-item">{{ strtoupper($detalle->notas) }}</div>
                            @endif
                        </td>
                        <td style="text-align: right; padding-top: 6px; font-size: 14px; font-weight: bold;">
                            ${{ number_format($precioLinea, 2) }}
                        </td>
                    </tr>
                    <tr><td colspan="2" style="height: 4px;"></td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="dashed-line"></div>

    {{-- Total --}}
    @php
        $totalPrecuenta = $detalles->sum(fn($d) => ($d->precio_unitario ?? 0) * $d->cantidad);
    @endphp
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 5px 0; font-size: 18px; font-weight: bold; border-bottom: 1px dashed #000;">
        <span>TOTAL:</span>
        <span>${{ number_format($totalPrecuenta, 2) }}</span>
    </div>

    {{-- Footer --}}
    <div class="text-center mt-1" style="margin-top: 12px; font-size: 11px; line-height: 1.5;">
        ESTA PRE-CUENTA ES SOLO INFORMATIVA.<br>
        SOLICITA TU TICKET DE PAGO.
    </div>

    <script>
        // La impresión se maneja desde el modal del sistema.
    </script>

</body>
</html>