<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mr. Feg')</title>

    <!-- Favicon tradicional (para la pestaña del navegador) -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Iconos de alta resolución para Web Apps, Barra de tareas y Accesos directos -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/mrlogo.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('images/mrlogo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/mrlogo.png') }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/js/app.js'])

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        'luxury-bg': 'var(--bg-color)',      
                        'luxury-card': 'var(--card-color)',    
                        'luxury-border': 'var(--border-color)',  
                        'luxury-accent': '#3B82F6',  
                        'luxury-text': 'var(--text-color)',    
                        'luxury-muted': 'var(--text-muted)',   
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            /* Paleta principal azul */
            --blue-950: #020617;
            --blue-900: #0b1e3f;
            --blue-800: #123064;
            --blue-700: #1a4694;
            --blue-500: #2f7bf0;
            --blue-400: #5b9bff;

            /* Paleta naranja/acento */
            --coral-400: #fb923c;
            --coral-500: #f97316;
            --coral-700: #c2410c;

            --bg-gradient: linear-gradient(to bottom, #38b6ff 0%, #1573b0 50%, #0a4670 100%);

            --bg-color: var(--blue-950);
            --sidebar-bg: var(--blue-900);
            --header-bg: rgba(11,30,63,0.6); /* Ajustado para cristal oscuro */
            --card-color: var(--blue-900);
            --text-color: #F8FAFC; /* text-slate-100 */
            --text-muted: #bfdbfe; /* azul claro / text-blue-200 */
            --border-color: rgba(18,48,100,0.18);
            --glass-bg: linear-gradient(145deg, rgba(11,30,63,0.88) 0%, rgba(2,6,23,0.72) 100%);
            --glass-hover: linear-gradient(145deg, rgba(15,36,66,0.95) 0%, rgba(6,18,40,0.85) 100%);
            --input-bg: rgba(11,30,63,0.6);
            --panel-card: rgba(11,30,63,0.85);
            --panel-border: rgba(18,48,100,0.20);

            /* Texto cálido para el header claro (no toca ningún azul,
               solo se usa donde el texto va sobre el degradado azul claro) */
            --header-text: #fff7ed;
            --header-text-hover: #fee8cc;
        }

        body { background: var(--bg-gradient); min-height: 100vh; font-family: 'Inter', sans-serif; color: var(--text-color); overflow-x: hidden; margin: 0; padding: 0; }
        
        .glass-card { backdrop-filter: blur(14px); border: 1px solid var(--blue-800); background-color: rgba(11,30,63,0.75); box-shadow: 0 12px 30px rgba(0,0,0,0.25); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        
        ::-webkit-scrollbar { width: 4px; } 
        ::-webkit-scrollbar-track { background: transparent; } 
        ::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }

        /* ===== ESTILOS DEL TOAST GLOBAL ===== */
        @keyframes shrink-bar {
            from { width: 100%; }
            to { width: 0%; }
        }
        .animate-shrink {
            animation: shrink-bar 3s linear forwards;
        }
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

    <!-- Fondos desenfocados decorativos -->
    <div class="fixed top-[-20%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-blue-600/5 blur-[150px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-20%] right-[-10%] w-[40vw] h-[40vw] rounded-full bg-orange-600/5 blur-[150px] pointer-events-none z-0"></div>

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
            const colorGradiente = esExito ? 'from-[var(--coral-400)] to-[var(--coral-500)]' : 'from-rose-400 to-red-500';
            const icono = esExito ? 'fa-check' : 'fa-exclamation';
            const titulo = esExito ? 'Operación Exitosa' : 'Atención';

            const div = document.createElement('div');
            div.id = id;
            div.className = 'relative overflow-hidden bg-[var(--card-color)] border border-[var(--panel-border)] rounded-2xl shadow-lg p-4 flex gap-3.5 items-start w-[320px] transition-all duration-300 transform translate-x-0 opacity-100';
            div.innerHTML = `
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r ${colorGradiente}"></div>
                <div class="flex items-center justify-center w-8 h-8 rounded-full border border-[var(--coral-500)]/30 bg-[var(--coral-500)]/10 text-[var(--coral-500)] shadow-[0_0_15px_rgba(249,115,22,0.12)] flex-shrink-0 mt-1">
                    <i class="fas ${icono} text-[11px]"></i>
                </div>
                <div class="flex-1 pr-3">
                    <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[var(--coral-500)] mb-1">${titulo}</p>
                    <p class="text-[13px] font-bold text-[var(--text-color)] leading-tight">${mensaje}</p>
                </div>
                <button onclick="cerrarToast('${id}')" class="absolute top-3.5 right-3.5 text-gray-400 hover:text-white transition-colors outline-none">
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
                <div class="bg-[var(--card-color)] border border-[var(--border-color)] rounded-[2rem] shadow-2xl w-full max-w-sm p-6">
                    <h2 class="text-lg font-black text-[var(--text-color)] tracking-tight mb-2">${titulo}</h2>
                    <p class="text-sm text-[var(--text-muted)] font-medium mb-6">${mensaje}</p>
                    <div class="flex justify-end gap-3">
                        <button id="confirmCancelarBtn" class="px-5 py-2.5 rounded-xl border border-[var(--border-color)] text-xs font-bold text-[var(--text-color)] hover:bg-white/5 transition outline-none">Cancelar</button>
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

    <div id="teclado-virtual-contenedor" class="teclado-virtual-contenedor oculto">
        <div class="simple-keyboard"></div>
    </div>
</body>
</html>