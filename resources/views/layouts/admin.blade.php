<!DOCTYPE html>
<html lang="es"> <!-- QUITO LA CLASE "dark" AQUÍ -->
<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mr. Feg')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/js/app.js'])

    <style>
        /* 1. VARIABLES PARA MODO CLARO (Por defecto) */
        :root {
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --card-color: #ffffff;
            --text-color: #0f172a; /* Slate 900 */
            --text-muted: #64748b; /* Slate 500 */
            --border-color: #e2e8f0;
            --header-bg: rgba(255, 255, 255, 0.8);
            --panel-card: #ffffff;
            --panel-border: #e2e8f0;
        }

        /* 2. VARIABLES SI SE ACTIVA MODO OSCURO (Solo si agregas la clase .dark al html) */
        .dark {
            --bg-color: #020617;
            --sidebar-bg: #0b1e3f;
            --card-color: #0b1e3f;
            --text-color: #F8FAFC;
            --text-muted: #bfdbfe;
            --border-color: rgba(18,48,100,0.18);
            --header-bg: rgba(11,30,63,0.6);
            --panel-card: rgba(11,30,63,0.85);
            --panel-border: rgba(18,48,100,0.20);
        }

        body { 
            background-color: var(--bg-color) !important; 
            min-height: 100vh; 
            font-family: 'Inter', sans-serif; 
            color: var(--text-color); 
            overflow-x: hidden; 
        }

        /* Aplicar las variables a las clases de Tailwind */
        .bg-luxury-bg { background-color: var(--bg-color); }
        .bg-luxury-card { background-color: var(--card-color); }
        .border-luxury-border { border-color: var(--border-color); }
        
        ::-webkit-scrollbar { width: 4px; } 
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
    </style>

    @stack('styles')
</head>
<body class="selection:bg-[#3B82F6]/30 selection:text-[var(--text-color)]"
      data-usuario-id="{{ auth()->id() }}">

    <script>
        // Recuperar estado del Sidebar inmediatamente para evitar parpadeos
        const sidebarGuardado = localStorage.getItem('sidebar-ollintem');
        if (sidebarGuardado === 'expandido') {
            document.body.classList.add('sidebar-expandido');
        }
    </script>

    

    {{-- ===== TOAST GLOBAL DE ALERTAS ===== --}}
    <div id="toastContainerGlobal" class="fixed top-6 right-6 z-[100] flex flex-col gap-4">
        @if(session('success'))
            <div id="toast-exito" class="relative overflow-hidden bg-[var(--card-color)] border border-[var(--panel-border)] rounded-2xl shadow-lg p-4 flex gap-3.5 items-start w-[320px] transition-all duration-300 transform translate-x-0 opacity-100">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-400 to-cyan-400"></div>
                <div class="flex items-center justify-center w-8 h-8 rounded-full border border-emerald-500/30 bg-emerald-500/10 text-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.15)] flex-shrink-0 mt-1">
                    <i class="fas fa-check text-[11px]"></i>
                </div>
                <div class="flex-1 pr-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-400 mb-1">Operación Exitosa</p>
                    <p class="text-[13px] font-bold text-[var(--text-color)] leading-tight">{{ session('success') }}</p>
                </div>
                <button onclick="cerrarToast('toast-exito')" class="absolute top-3.5 right-3.5 text-gray-400 hover:text-white transition-colors outline-none">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-emerald-400 to-cyan-400 animate-shrink"></div>
            </div>
        @endif

        @if(session('error'))
            <div id="toast-error" class="relative overflow-hidden bg-[var(--card-color)] border border-[var(--panel-border)] rounded-2xl shadow-lg p-4 flex gap-3.5 items-start w-[320px] transition-all duration-300 transform translate-x-0 opacity-100">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-400 to-red-500"></div>
                <div class="flex items-center justify-center w-8 h-8 rounded-full border border-rose-500/30 bg-rose-500/10 text-rose-400 shadow-[0_0_15px_rgba(244,63,94,0.15)] flex-shrink-0 mt-1">
                    <i class="fas fa-exclamation text-[11px]"></i>
                </div>
                <div class="flex-1 pr-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-rose-400 mb-1">Atención</p>
                    <p class="text-[13px] font-bold text-[var(--text-color)] leading-tight">{{ session('error') }}</p>
                </div>
                <button onclick="cerrarToast('toast-error')" class="absolute top-3.5 right-3.5 text-gray-400 hover:text-white transition-colors outline-none">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-rose-400 to-red-500 animate-shrink"></div>
            </div>
        @endif

        @if($errors->any())
            <div id="toast-validacion" class="relative overflow-hidden bg-[var(--card-color)] border border-[var(--panel-border)] rounded-2xl shadow-lg p-4 flex gap-3.5 items-start w-[320px] transition-all duration-300 transform translate-x-0 opacity-100">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-rose-400 to-red-500"></div>
                <div class="flex items-center justify-center w-8 h-8 rounded-full border border-rose-500/30 bg-rose-500/10 text-rose-400 shadow-[0_0_15px_rgba(244,63,94,0.15)] flex-shrink-0 mt-1">
                    <i class="fas fa-exclamation text-[11px]"></i>
                </div>
                <div class="flex-1 pr-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-rose-400 mb-1">Datos inválidos</p>
                    <p class="text-[13px] font-bold text-[var(--text-color)] leading-tight">{{ $errors->first() }}</p>
                </div>
                <button onclick="cerrarToast('toast-validacion')" class="absolute top-3.5 right-3.5 text-gray-400 hover:text-white transition-colors outline-none">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-rose-400 to-red-500 animate-shrink"></div>
            </div>
        @endif
    </div>

    @hasSection('no-sidebar')
        <main class="flex-1 relative z-10 flex flex-col min-h-screen">
            @yield('content')
        </main>
    @else
        <div class="flex h-screen overflow-hidden relative z-10">
            @include('layouts.sidebar')

            <main class="flex-1 overflow-y-auto min-w-0 relative z-10 flex flex-col">
                <header class="backdrop-blur-2xl border-b sticky top-0 z-30 px-4 sm:px-6 lg:px-10 py-4 lg:py-5 flex justify-between items-center gap-3 bg-transparent border-transparent">
                    <div class="flex items-center gap-3 min-w-0">
                        <button id="mobileMenuBtn" type="button" aria-label="Abrir menú" class="lg:hidden shrink-0 w-9 h-9 rounded-full bg-[var(--sidebar-bg)] border border-[var(--border-color)] flex items-center justify-center text-[var(--text-muted)] hover:text-[var(--text-color)] transition-all shadow-inner">
                            <i class="fas fa-bars text-sm"></i>
                        </button>
                        <div class="flex flex-col min-w-0">
                            <h1 class="text-lg sm:text-xl lg:text-[22px] font-black text-[var(--header-text)] tracking-tight truncate">@yield('header-title', 'Gestión de Personal')</h1>
                            <p class="text-[11px] text-[var(--header-text)]/75 font-semibold mt-1 truncate hidden sm:block">@yield('header-subtitle', 'Administra tu sistema')</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 shrink-0">
                        @hasSection('header-actions')
                            <div class="hidden md:flex items-center gap-3">@yield('header-actions')</div>
                        @endif
                        <!-- Botón de cambio de tema eliminado correctamente -->
                    </div>
                </header>
                @yield('content')
            </main>
        </div>
    @endif

    @yield('modals')

    <script>
        /**
         * Función global para alternar dropdowns personalizados en todo el sistema.
         */
        function toggleDropdown(menuId, event) {
            if (event) event.stopPropagation();

            // Cierra cualquier otro menú que termine en 'Menu'
            const allMenus = document.querySelectorAll('[id$="Menu"]');
            const targetMenu = document.getElementById(menuId);
            if (!targetMenu) return;

            const isOpen = !targetMenu.classList.contains('hidden');

            // Oculta todos los menús abiertos
            allMenus.forEach(menu => menu.classList.add('hidden'));

            // Alterna el estado del menú seleccionado
            if (!isOpen) {
                targetMenu.classList.remove('hidden');
            }
        }

        // Cierre global al hacer clic fuera de cualquier dropdown
        window.addEventListener('click', () => {
            document.querySelectorAll('[id$="Menu"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        });

        /**
         * Abre cualquier modal de forma genérica con su animación Soft Light
         */
        window.openModal = function(modalId, containerId) {
            const modal = document.getElementById(modalId);
            const container = document.getElementById(containerId);
            if (!modal || !container) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
        };

        /**
         * Cierra cualquier modal de forma genérica
         */
        window.closeModal = function(modalId, containerId) {
            const modal = document.getElementById(modalId);
            const container = document.getElementById(containerId);
            if (!container) return;

            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            }, 300);
        };

        // Cierre automático de modales con la tecla ESC
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modalesVisibles = document.querySelectorAll('.fixed.inset-0:not(.hidden)');
                modalesVisibles.forEach(modal => {
                    const btnCancelar = modal.querySelector('button[onclick*="close"], button[onclick*="cerrar"]');
                    if (btnCancelar) {
                        btnCancelar.click();
                    } else {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                });
            }
        });

        // --- GESTIÓN DEL SIDEBAR ---
        function toggleSidebar() {
            const body = document.body;
            body.classList.toggle('sidebar-expandido');
            if (body.classList.contains('sidebar-expandido')) {
                localStorage.setItem('sidebar-ollintem', 'expandido');
            } else {
                localStorage.setItem('sidebar-ollintem', 'minimizado');
            }
        }

        // --- GESTIÓN DEL TOAST DE SESIÓN ---
        function cerrarToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.classList.remove('translate-x-0', 'opacity-100');
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                if (document.getElementById('toast-exito')) cerrarToast('toast-exito');
                if (document.getElementById('toast-error')) cerrarToast('toast-error');
                if (document.getElementById('toast-validacion')) cerrarToast('toast-validacion');
            }, 3000);
        });

        // --- TOAST DINÁMICO ---
        function crearToastElemento(mensaje, tipo) {
            const id = 'toast-' + Date.now();
            const esExito = tipo !== 'error';
            const colorGradiente = esExito ? 'from-emerald-400 to-cyan-400' : 'from-rose-400 to-red-500';
            const colorIcono = esExito ? 'emerald' : 'rose';
            const icono = esExito ? 'fa-check' : 'fa-exclamation';
            const titulo = esExito ? 'Operación Exitosa' : 'Atención';

            const div = document.createElement('div');
            div.id = id;
            div.className = 'relative overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-lg p-4 flex gap-3.5 items-start w-[320px] transition-all duration-300 transform translate-x-0 opacity-100';
            div.innerHTML = `
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r ${colorGradiente}"></div>
                <div class="flex items-center justify-center w-8 h-8 rounded-full border border-${colorIcono}-500/30 bg-${colorIcono}-500/10 text-${colorIcono}-500 shadow-[0_0_15px_rgba(16,185,129,0.15)] flex-shrink-0 mt-1">
                    <i class="fas ${icono} text-[11px]"></i>
                </div>
                <div class="flex-1 pr-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-${colorIcono}-600 mb-1">${titulo}</p>
                    <p class="text-[13px] font-bold text-slate-800 leading-tight">${mensaje}</p>
                </div>
                <button onclick="cerrarToast('${id}')" class="absolute top-3.5 right-3.5 text-gray-400 hover:text-gray-600 transition-colors outline-none">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r ${colorGradiente} animate-shrink"></div>
            `;
            return { div, id };
        }

        window.showToast = function(mensaje, tipo = 'success') {
            const contenedor = document.getElementById('toastContainerGlobal');
            if (!contenedor) return;
            const { div, id } = crearToastElemento(mensaje, tipo);
            contenedor.appendChild(div);
            setTimeout(() => cerrarToast(id), 3000);
        };

        // --- CONFIRMACIÓN MODAL ---
        window.showConfirm = function(mensaje, onConfirm, opciones = {}) {
            const titulo = opciones.titulo || '¿Estás seguro?';
            const textoConfirmar = opciones.textoConfirmar || 'Confirmar';
            const colorBtn = opciones.peligro === false
                ? 'bg-[#3B82F6] hover:bg-[#2563EB]'
                : 'bg-rose-600 hover:bg-rose-500';

            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 z-[200] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4';
            overlay.innerHTML = `
                <div class="bg-white border border-slate-200 rounded-[2rem] shadow-2xl w-full max-w-sm p-6">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight mb-2">${titulo}</h2>
                    <p class="text-sm text-slate-500 font-medium mb-6">${mensaje}</p>
                    <div class="flex justify-end gap-3">
                        <button id="confirmCancelarBtn" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition outline-none">Cancelar</button>
                        <button id="confirmAceptarBtn" class="px-5 py-2.5 rounded-xl ${colorBtn} text-white text-xs font-black uppercase tracking-widest transition outline-none shadow-sm">${textoConfirmar}</button>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);

            const cerrar = () => overlay.remove();
            overlay.querySelector('#confirmCancelarBtn').addEventListener('click', cerrar);
            overlay.querySelector('#confirmAceptarBtn').addEventListener('click', () => {
                cerrar();
                onConfirm();
            });
        };
    </script>
    @stack('scripts')

    {{-- TECLADO VIRTUAL — comentado temporalmente. Descomentar para reactivar.
    <div id="teclado-virtual-contenedor" class="teclado-virtual-contenedor oculto">
        <div class="simple-keyboard"></div>
    </div>
    --}}
</body>
</html>