{{-- Estilos para manejo de teclado virtual en PC --}}
<style>
    @media (min-width: 768px) {
        body.teclado-virtual-abierto #editEmpleadoModal {
            align-items: flex-start !important;
            padding-top: 15px !important;
        }
        
        body.teclado-virtual-abierto #editModalContent {
            transform: translateY(0) scale(0.98) !important;
            max-height: calc(100dvh - 340px) !important; 
        }
    }
</style>

<div id="editEmpleadoModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[99999] flex items-center justify-center p-4 hidden opacity-0 transition-all duration-300">
    
    {{-- Contenedor principal con estilo Soft Light --}}
    <div class="relative bg-[#F2F2F2] border border-slate-200 rounded-[2rem] w-full max-w-[480px] transform scale-95 opacity-0 transition-all duration-300 shadow-2xl flex flex-col max-h-[85vh] sm:max-h-[90vh] overflow-hidden" id="editModalContent">
        
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- BOTÓN CERRAR (X) --}}
        <button type="button" onclick="closeModal('editEmpleadoModal', 'editModalContent')" class="absolute top-5 right-5 sm:top-8 sm:right-8 text-slate-400 hover:text-rose-500 hover:rotate-90 transition-all duration-300 outline-none z-50 bg-white border border-slate-200 rounded-xl w-9 h-9 flex items-center justify-center cursor-pointer shadow-sm active:scale-95">
            <i class="fas fa-times text-base pointer-events-none"></i>
        </button>

        {{-- Cabecera fija --}}
        <div class="px-6 pt-6 sm:px-8 sm:pt-8 pb-4 relative z-10 flex-shrink-0">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Editar Perfil</h2>
            <p class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Actualización de credenciales</p>
        </div>

        <form id="formEditar" action="#" method="POST" class="flex flex-col flex-1 min-h-0 bg-[#F2F2F2]">
            @csrf
            @method('PUT')
            
            {{-- Cuerpo con Scroll Interno --}}
            <div class="px-6 sm:px-8 space-y-4 overflow-y-auto flex-1 pb-4 overscroll-contain bg-[#F2F2F2]" style="-webkit-overflow-scrolling: touch;">
                
                {{-- AVISO DE PROTECCIÓN --}}
                <div id="alertaProteccion" class="hidden p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-3 shadow-sm">
                    <i class="fas fa-shield-check text-blue-600 text-base sm:text-lg mt-0.5"></i>
                    <div>
                        <p class="text-[9px] sm:text-[10px] font-black text-blue-700 uppercase tracking-widest">Protección de Cuenta</p>
                        <p class="text-[11px] sm:text-xs font-medium text-blue-600 mt-0.5">Por seguridad, no puedes quitarte el acceso ni cambiarte tu propio rol.</p>
                    </div>
                </div>

                {{-- Nombre --}}
                <div>
                    <label for="edit_nombre" class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1 block">Nombre Completo</label>
                    <div class="relative group">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                        <input type="text" id="edit_nombre" name="nombre" data-teclado="texto" required 
                            class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 text-sm font-semibold text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all placeholder:text-slate-400">
                    </div>
                </div>

                {{-- Switch de Acceso --}}
                <div id="cajaAcceso" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200 shadow-sm transition-all group">
                    <div class="flex flex-col pointer-events-none pr-4">
                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Acceso al Sistema</span>
                        <p class="text-[11px] font-medium text-slate-500 mt-0.5">¿Este empleado usará la plataforma?</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                        <input type="checkbox" id="edit_toggleAcceso" name="puede_acceder_pos" value="1" onchange="toggleEditAccesoFields()" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer 
                                peer-checked:after:translate-x-full peer-checked:after:border-white 
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                after:bg-white after:border after:border-slate-300 
                                after:rounded-full after:h-5 after:w-5 
                                after:transition-all after:duration-300 
                                peer-checked:bg-blue-600 shadow-inner
                                transition-all duration-300"></div>
                    </label>
                </div>

                {{-- Contenedor Condicional (PIN y ROL) --}}
                <div id="edit_accesoFields" class="hidden space-y-4 transition-all duration-300">
                    
                    {{-- PIN --}}
                    <div>
                        <label for="edit_codigo_empleado" class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1 block">PIN de Seguridad (4 dígitos)</label>
                        <div class="relative group">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                            <input type="text" id="edit_codigo_empleado" name="codigo_empleado" data-teclado="numerico" maxlength="4" 
                                class="w-full h-12 bg-white border border-slate-200 rounded-xl pl-11 pr-4 font-black tracking-[0.8em] text-base text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 shadow-sm transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    {{-- Rol (Dropdown Global con sufijo Menu) --}}
                    <div class="relative group" id="cajaRol">
                        <label class="text-[10px] font-black text-slate-600 uppercase tracking-widest mb-1.5 ml-1 block">Rol del Sistema</label>
                        <input type="hidden" name="rol_id" id="edit_rol_id_input">
                        <button type="button" onclick="toggleDropdown('editRoleMenu', event)" id="editDropdownBtn" 
                            class="flex items-center justify-between w-full h-12 bg-white border border-slate-200 rounded-xl pl-4 pr-4 text-xs sm:text-sm font-bold text-slate-800 outline-none hover:border-blue-300 shadow-sm transition-all">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-shield-alt text-slate-400"></i>
                                <span id="editDropdownSelected" class="text-slate-700">Seleccionar...</span>
                            </div>
                            <i class="fas fa-chevron-down text-slate-400 text-xs"></i>
                        </button>
                        
                        {{-- Dropdown Menu con sufijo Menu para que el layout lo controle globalmente --}}
                        <div id="editRoleMenu" class="absolute w-full bg-white border border-slate-200 rounded-xl shadow-xl z-[110] py-2 hidden mt-2">
                            @foreach($roles ?? [] as $rol)
                                <button type="button" onclick="selectEditRole('{{ $rol->nombre }}', '{{ $rol->id }}')" 
                                    class="w-full px-4 py-2.5 text-left text-xs sm:text-sm hover:bg-blue-50 font-semibold text-slate-700 hover:text-blue-700 transition-colors">
                                    {{ $rol->nombre }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botones Inferiores --}}
            <div class="px-6 sm:px-8 py-4 border-t border-slate-200/60 bg-slate-100/50 flex items-center justify-between flex-shrink-0" style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
                <button type="button" onclick="closeModal('editEmpleadoModal', 'editModalContent')" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors outline-none active:scale-95">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md shadow-blue-500/20 hover:shadow-lg transition-all active:scale-95 outline-none">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const idUsuarioActual = {{ auth()->id() ?? 'null' }};

    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('editEmpleadoModal');
        if (modalElement) {
            document.body.appendChild(modalElement);
        }
    });

    function toggleEditAccesoFields() {
        const checkbox = document.getElementById('edit_toggleAcceso');
        const fields = document.getElementById('edit_accesoFields');
        if(checkbox && fields) {
            fields.classList.toggle('hidden', !checkbox.checked);
        }
    }

    function selectEditRole(nombre, id) {
        document.getElementById('editDropdownSelected').innerText = nombre;
        document.getElementById('edit_rol_id_input').value = id;
        document.getElementById('editRoleMenu').classList.add('hidden');
    }

    window.ejecutarEditar = function(btn) {
        const id = btn.getAttribute('data-id');
        const nombre = btn.getAttribute('data-nombre');
        const codigo = btn.getAttribute('data-codigo');
        const rolId = btn.getAttribute('data-rol-id');
        const rolNombre = btn.getAttribute('data-rol-nombre');
        const tieneAcceso = btn.getAttribute('data-acceso');

        const form = document.getElementById('formEditar');
        const toggle = document.getElementById('edit_toggleAcceso');
        
        const alertaProteccion = document.getElementById('alertaProteccion');
        const cajaAcceso = document.getElementById('cajaAcceso');
        const cajaRol = document.getElementById('cajaRol');

        if(form) form.action = `/admin/empleados/${id}`;
        
        if(document.getElementById('edit_nombre')) document.getElementById('edit_nombre').value = nombre || '';
        if(document.getElementById('edit_codigo_empleado')) document.getElementById('edit_codigo_empleado').value = codigo || '';
        if(document.getElementById('edit_rol_id_input')) document.getElementById('edit_rol_id_input').value = rolId || '';
        if(document.getElementById('editDropdownSelected')) document.getElementById('editDropdownSelected').innerText = rolNombre || 'Seleccionar...';
        
        if(toggle) {
            toggle.checked = (tieneAcceso == 1 || tieneAcceso == "1");
            toggleEditAccesoFields();
        }

        if (typeof idUsuarioActual !== 'undefined' && idUsuarioActual && parseInt(id) === parseInt(idUsuarioActual)) {
            if(alertaProteccion) alertaProteccion.classList.remove('hidden');
            if(cajaAcceso) cajaAcceso.classList.add('pointer-events-none', 'opacity-50', 'grayscale');
            if(cajaRol) cajaRol.classList.add('pointer-events-none', 'opacity-50', 'grayscale');
        } else {
            if(alertaProteccion) alertaProteccion.classList.add('hidden');
            if(cajaAcceso) cajaAcceso.classList.remove('pointer-events-none', 'opacity-50', 'grayscale');
            if(cajaRol) cajaRol.classList.remove('pointer-events-none', 'opacity-50', 'grayscale');
        }

        // Usamos la función global openModal definida en el layout principal
        openModal('editEmpleadoModal', 'editModalContent');
    };
</script>