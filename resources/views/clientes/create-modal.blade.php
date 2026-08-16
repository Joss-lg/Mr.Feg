{{-- MODAL: Registrar Nuevo Cliente --}}
<div id="modalCrearCliente" class="hidden fixed inset-0 z-[60] items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div id="clienteModalContainer" class="bg-white border border-slate-200 rounded-[2rem] shadow-xl w-full max-w-4xl max-h-[92vh] overflow-hidden scale-95 opacity-0 transition-all duration-200">
    <div class="max-h-[92vh] overflow-y-auto p-6 sm:p-10">

        {{-- Header del Modal --}}
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 flex items-center justify-center rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 shrink-0 shadow-sm">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <div class="space-y-0.5">
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">Registrar Nuevo Cliente</h2>
                    <p class="text-xs font-medium text-slate-500">Completa la información personal y la dirección de entrega.</p>
                </div>
            </div>
            <button type="button" onclick="window.closeModal('modalCrearCliente', 'clienteModalContainer')" class="group w-10 h-10 shrink-0 flex items-center justify-center rounded-xl bg-slate-50 border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all shadow-sm outline-none">
                <i class="fas fa-times text-sm transition-transform duration-300 group-hover:rotate-90"></i>
            </button>
        </div>

        {{-- Mensajes de Error de Validación --}}
        @if ($errors->any())
            <div class="mb-6 px-5 py-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl shadow-sm text-xs font-bold space-y-2">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
                    <span>¡Ups! Hubo un problema con tus datos:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- SECCIÓN 1: DATOS DEL CLIENTE --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 text-xs font-black">1</div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Datos del Cliente</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nombre --}}
                    <div class="space-y-2">
                        <label for="nombre" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Nombre(s) *</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required data-teclado="texto" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. Juan Carlos">
                    </div>

                    {{-- Apellido --}}
                    <div class="space-y-2">
                        <label for="apellido" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Apellido(s)</label>
                        <input type="text" name="apellido" id="apellido" value="{{ old('apellido') }}" data-teclado="texto" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. Pérez Gómez">
                    </div>

                    {{-- Teléfono --}}
                    <div class="space-y-2 md:col-span-2">
                        <label for="telefono" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}" data-teclado="numerico" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. 5512345678">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN 2: DIRECCIÓN DE ENTREGA --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100 text-xs font-black">2</div>
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Dirección de Entrega</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {{-- Calle --}}
                    <div class="space-y-2 md:col-span-2 lg:col-span-3">
                        <label for="calle" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Calle y Número *</label>
                        <input type="text" name="calle" id="calle" value="{{ old('calle') }}" required data-teclado="texto" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. Av. Hidalgo 123">
                    </div>

                    {{-- Manzana --}}
                    <div class="space-y-2">
                        <label for="manzana" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Manzana</label>
                        <input type="text" name="manzana" id="manzana" value="{{ old('manzana') }}" data-teclado="texto" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. Mz 45">
                    </div>

                    {{-- Lote --}}
                    <div class="space-y-2">
                        <label for="lote" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Lote</label>
                        <input type="text" name="lote" id="lote" value="{{ old('lote') }}" data-teclado="texto" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. Lt 12">
                    </div>

                    {{-- Colonia --}}
                    <div class="space-y-2">
                        <label for="colonia" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Colonia</label>
                        <input type="text" name="colonia" id="colonia" value="{{ old('colonia') }}" data-teclado="texto" autocomplete="off"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl px-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm"
                            placeholder="Ej. Centro">
                    </div>

                    {{-- Referencia --}}
                    <div class="space-y-2 md:col-span-2 lg:col-span-3">
                        <label for="referencia" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest ml-1">Referencia del Domicilio</label>
                        <textarea name="referencia" id="referencia" rows="2" data-teclado="texto" autocomplete="off"
                            class="w-full bg-white border border-slate-200 rounded-xl p-4 text-sm font-semibold text-slate-800 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm resize-none"
                            placeholder="Ej. Casa verde de dos pisos, portón negro...">{{ old('referencia') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- BOTONES DE ACCIÓN --}}
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                <button type="button" onclick="window.closeModal('modalCrearCliente', 'clienteModalContainer')"
                    class="px-6 h-12 rounded-xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all flex items-center justify-center outline-none">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 h-12 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all active:scale-95 flex items-center justify-center gap-2 outline-none">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

@if ($errors->any())
    {{-- Si la petición regresó con errores de validación, reabrimos el modal automáticamente --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.openModal === 'function') {
                window.openModal('modalCrearCliente', 'clienteModalContainer');
            }
        });
    </script>
@endif