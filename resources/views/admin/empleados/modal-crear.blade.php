{{-- Estilos para manejo de teclado virtual en PC --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #modalCrearEmpleado {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        
        body.teclado-virtual-abierto #modalCrearContent {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important; 
        }
    }
</style>

{{-- CONTENEDOR PRINCIPAL DEL MODAL --}}
<div id="modalCrearEmpleado" class="fixed inset-0 z-[99999] hidden bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4 transition-all duration-300">
    
    {{-- Tarjeta Estilo Soft Light --}}
    <div class="bg-[#F2F2F2] border border-slate-200 w-full max-w-md rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300 flex flex-col max-h-[88dvh] sm:max-h-[90dvh]" id="modalCrearContent">
        
        <form id="formCrearEmpleado" method="POST" action="{{ route('admin.empleados.store') }}" class="flex flex-col h-full relative z-10 overflow-hidden bg-[#F2F2F2]">
            @csrf
            
            {{-- Cuerpo del modal --}}
            <div class="p-5 sm:p-8 overflow-y-auto flex-1 space-y-4 bg-[#F2F2F2] overscroll-contain scrollbar-thin" style="-webkit-overflow-scrolling: touch;">
                
                {{-- Encabezado con la "X" animada --}}
                <div class="flex justify-between items-start pb-2 gap-3">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 flex items-center gap-2 tracking-tight">
                            Registrar Perfil <i class="fas fa-user-plus text-blue-600 text-base"></i>
                        </h3>
                        <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-1">Nueva credencial de acceso</p>
                    </div>
                    <button type="button" onclick="closeModal('modalCrearEmpleado', 'modalCrearContent')" class="text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:border-rose-200 hover:bg-rose-50 outline-none flex-shrink-0 shadow-sm active:scale-95">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                {{-- Campo: Nombre Completo --}}
                <div>
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Nombre Completo</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-slate-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                        </div>
                        <input type="text" name="nombre" id="crear_nombre" required placeholder="Ej. Juan Pérez" autocomplete="off" data-teclado="texto"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-sm font-semibold text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all placeholder:text-slate-400 shadow-sm">
                    </div>
                </div>

                {{-- Campo: Rol del Sistema (DROPDOWN PERSONALIZADO GLOBAL) --}}
                <div class="relative group" id="cajaRol">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1">Rol del Sistema</label>
                    
                    <input type="hidden" name="rol_id" id="crear_rol_id_input" required>
                    
                    <button type="button" onclick="toggleDropdown('crearDropdownMenu', event)" id="crearDropdownBtn" 
                        class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-sm font-semibold text-slate-800 outline-none hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-shield-alt text-slate-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                            <span id="crearDropdownSelected" class="text-slate-400">Seleccionar rol...</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                    </button>
                    
                    {{-- Usamos el sufijo 'Menu' para que el layout global lo gestione de forma automática --}}
                    <div id="crearDropdownMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2">
                        @foreach($roles ?? [] as $rol)
                            <button type="button" onclick="selectRole('{{ $rol->nombre }}', '{{ $rol->id }}')" 
                                class="w-full px-5 py-3 text-left text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                {{ $rol->nombre }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Switch: Acceso al Sistema --}}
                <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-2xl shadow-sm gap-3">
                    <div class="min-w-0 flex-1">
                        <label class="block text-xs font-black text-slate-800 uppercase tracking-wider leading-tight">Acceso al sistema</label>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5 truncate">¿Usará la plataforma?</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 select-none">
                        <input type="checkbox" name="puede_acceder_pos" id="crear_acceso" value="1" onchange="toggleCrearAccesoFields()" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                    </label>
                </div>

                {{-- Contenedor dinámico: PIN --}}
                <div id="crear_accesoFields" class="hidden transition-all duration-300 space-y-1.5">
                    <label class="block text-[10px] font-black text-slate-600 uppercase tracking-widest ml-1">PIN de Seguridad (4 dígitos)</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-slate-400 group-focus-within:text-blue-500 transition-colors text-sm"></i>
                        </div>
                        <input type="password" name="codigo_empleado" id="crear_codigo" maxlength="4" pattern="[0-9]*" placeholder="••••" autocomplete="new-password"
                            data-teclado="numerico" data-teclado-max="4" inputmode="none"
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-lg tracking-[0.4em] font-black text-slate-800 placeholder-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Barra inferior de acciones --}}
            <div class="px-6 py-4 border-t border-slate-200/60 bg-slate-100/50 flex items-center justify-between flex-shrink-0" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                <button type="button" onclick="closeModal('modalCrearEmpleado', 'modalCrearContent')" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors outline-none active:scale-95">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 outline-none">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('modalCrearEmpleado');
        if (modalElement) {
            document.body.appendChild(modalElement);
        }

        if (typeof TecladoVirtual !== 'undefined') {
            TecladoVirtual.attachAll();
        }
    });

    function selectRole(nombre, id) {
        document.getElementById('crearDropdownSelected').innerText = nombre;
        document.getElementById('crearDropdownSelected').classList.remove('text-slate-400');
        document.getElementById('crearDropdownSelected').classList.add('text-slate-800');
        document.getElementById('crear_rol_id_input').value = id;
        document.getElementById('crearDropdownMenu').classList.add('hidden');
    }

    function toggleCrearAccesoFields() {
        const checkbox = document.getElementById('crear_acceso');
        const fields = document.getElementById('crear_accesoFields');
        const inputPin = document.getElementById('crear_codigo');
        
        if (checkbox && fields && inputPin) {
            if (checkbox.checked) {
                fields.classList.remove('hidden');
                inputPin.setAttribute('required', 'required');
            } else {
                fields.classList.add('hidden');
                inputPin.removeAttribute('required');
                inputPin.value = '';
            }
        }
    }

    // Función auxiliar para resetear el formulario antes de abrirlo con la global openModal
    window.abrirModalCrearEmpleado = function() {
        const form = document.getElementById('formCrearEmpleado');
        if (form) form.reset();
        toggleCrearAccesoFields();
        
        document.getElementById('crearDropdownSelected').innerText = 'Seleccionar rol...';
        document.getElementById('crearDropdownSelected').classList.add('text-slate-400');
        document.getElementById('crearDropdownSelected').classList.remove('text-slate-800');

        // Llamamos a la función global del layout
        openModal('modalCrearEmpleado', 'modalCrearContent');
    }
</script>