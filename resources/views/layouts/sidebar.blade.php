<aside id="sidebar" class="w-[260px] bg-[#F2F2F2] backdrop-blur-2xl border-r border-slate-200/60 flex flex-col justify-between z-50 shrink-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)] overflow-x-hidden relative">
    
    {{-- LÓGICA CSS PURA (Adiós bugs visuales al contraer) --}}
    <style>
        #sidebar { transition: width 0.4s cubic-bezier(0.25, 1, 0.5, 1); }
        .sidebar-text { transition: opacity 0.2s ease, width 0.3s ease, margin 0.3s ease; white-space: nowrap; overflow: hidden; }
        
        /* --- ESTADO COLAPSADO (MEMORIA) --- */
        #sidebar.colapsado { width: 88px !important; }
        
        /* Textos se esfuman y no empujan las cajas */
        #sidebar.colapsado .sidebar-text { width: 0 !important; opacity: 0 !important; margin: 0 !important; pointer-events: none; }
        
        /* Menú de navegación: Centramos los iconos perfectos */
        #sidebar.colapsado .menu-link { padding-left: 0 !important; padding-right: 0 !important; justify-content: center !important; }
        
        /* Header: Ocultamos el logo suavemente y centramos la hamburguesa */
        #sidebar.colapsado .logo-wrapper { opacity: 0; pointer-events: none; position: absolute; }
        #sidebar.colapsado #toggleSidebar { right: 0; left: 0; margin: 0 auto; top: 50%; transform: translateY(-50%); }
        
        /* Footer: Modo mini y transparente */
        #sidebar.colapsado .user-footer { padding-left: 0; padding-right: 0; background: transparent; border-color: transparent; box-shadow: none; align-items: center; margin-bottom: 1rem; }
        
        /* Ajuste para el botón rojo cuadrado */
        #sidebar.colapsado .btn-logout { width: 44px !important; height: 44px !important; padding: 0 !important; justify-content: center !important; }

        /* =========================================================
           MODO MÓVIL (Drawer)
        ========================================================= */
        @media (max-width: 1023.98px) {
            #sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 84vw;
                max-width: 300px;
                transform: translateX(-100%);
                transition: transform 0.35s cubic-bezier(0.25, 1, 0.5, 1);
            }
            #sidebar.colapsado { width: 84vw; max-width: 300px; }
            #sidebar.colapsado .sidebar-text { width: auto !important; opacity: 1 !important; margin: 0 0 0 0.75rem !important; pointer-events: auto; }
            #sidebar.colapsado .logo-wrapper { opacity: 1; pointer-events: auto; position: relative; }
            #sidebar.colapsado .menu-link { padding-left: 0.75rem !important; padding-right: 0.75rem !important; justify-content: flex-start !important; }

            #sidebar.abierto { transform: translateX(0); }

            #sidebarOverlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.4); /* slate-900 con opacidad */
                backdrop-filter: blur(2px);
                z-index: 40;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            #sidebarOverlay.visible { display: block; opacity: 1; }
        }

        /* ANIMACIONES DE ENTRADA CASCADA PARA EL MENÚ */
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(-15px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in {
            animation: slideInRight 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; 
        }
    </style>

    {{-- Script para evitar parpadeo del sidebar --}}
    <script>
        (function() {
            if (window.matchMedia('(min-width: 1024px)').matches && localStorage.getItem('sidebarState') === 'collapsed') {
                document.getElementById('sidebar').classList.add('colapsado');
            }
        })();
    </script>

    {{-- Header del Logo --}}
    <div class="h-24 flex items-center px-5 relative shrink-0 w-full transition-all">
        <!-- Resplandor sutil azul detrás del logo -->
        <div class="absolute top-1/2 left-10 -translate-y-1/2 w-20 h-20 bg-blue-400/10 blur-[25px] rounded-full pointer-events-none"></div>
        
        <div class="logo-wrapper flex items-center relative z-10 w-full transition-opacity duration-300">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 p-[1px] shadow-[0_0_15px_rgba(59,130,246,0.2)] shrink-0 group hover:scale-105 transition-transform duration-300 cursor-pointer">
                <div class="w-full h-full bg-white rounded-[11px] flex items-center justify-center">
                    <img src="{{ asset('images/mrlogo.png') }}" alt="Logo" class="w-6 h-6 object-contain group-hover:rotate-12 transition-transform duration-300">
                </div>
            </div>
            <div class="sidebar-text ml-3 flex flex-col">
                <span class="font-black tracking-[0.15em] text-[15px] text-slate-900 leading-none">
                    Mr. Feg
                </span>
                <span class="text-[9px] text-blue-500 font-bold uppercase tracking-[0.25em] mt-1.5">Sistema Ventas</span>
            </div>
        </div>
        
        {{-- Botón colapsar --}}
        <button id="toggleSidebar" class="absolute right-4 top-7 w-8 h-8 flex items-center justify-center rounded-lg bg-white hover:bg-blue-50 text-slate-400 hover:text-blue-600 transition-colors cursor-pointer z-50 shrink-0 border border-slate-200/60 hover:border-blue-100 shadow-sm">
            <i class="fas fa-bars text-sm"></i>
        </button>
    </div>

   {{-- Navegación --}}
    <nav class="py-4 px-3 space-y-5 overflow-y-auto overflow-x-hidden max-h-[calc(100vh-14rem)] relative z-10 flex-1" id="nav-container">
        
        {{-- Estilos para la barra de scroll --}}
        <style>
            #nav-container::-webkit-scrollbar { width: 4px; }
            #nav-container::-webkit-scrollbar-track { background: transparent; }
            #nav-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            #nav-container:hover::-webkit-scrollbar-thumb { background: #94a3b8; }
        </style>

       @php
    $menu = [
        'Administración' => [
            ['route' => 'admin.dashboard', 'icon' => 'fas fa-th-large', 'label' => 'Dashboard', 'modulo_id' => 2],
            ['route' => 'admin.empleados.index', 'icon' => 'fas fa-users', 'label' => 'Empleados', 'modulo_id' => 4],
            ['route' => 'admin.roles.index', 'icon' => 'fas fa-id-badge', 'label' => 'Roles', 'modulo_id' => 12],
            ['route' => 'admin.finanzas.index', 'icon' => 'fas fa-chart-line', 'label' => 'Finanzas', 'modulo_id' => 11],
            ['route' => 'historial.index', 'icon' => 'fas fa-history', 'label' => 'Historial Cajas', 'modulo_id' => 13],
        ],
        'Productos' => [
            ['route' => 'admin.productos.index', 'icon' => 'fas fa-utensils', 'label' => 'Menú', 'modulo_id' => 5],
            ['route' => 'admin.inventario.index', 'icon' => 'fas fa-cube', 'label' => 'Inventario', 'modulo_id' => 3],
            ['route' => 'admin.categorias.index', 'icon' => 'fas fa-layer-group', 'label' => 'Categorías', 'modulo_id' => 6],
            ['route' => 'admin.promociones.index', 'icon' => 'fas fa-tags', 'label' => 'Promociones', 'modulo_id' => 8],
            ['route' => 'admin.fidelidad.index', 'icon' => 'fas fa-award', 'label' => 'Fidelidad', 'modulo_id' => 14],
        ],
        'Operaciones' => [
            ['route' => 'admin.cocina.index', 'icon' => 'fas fa-fire-burner', 'label' => 'Cocina', 'modulo_id' => 9],
            ['route' => 'admin.mesas.index', 'icon' => 'fas fa-chair', 'label' => 'Mesas', 'modulo_id' => 7],
            ['route' => 'admin.delivery.index', 'icon' => 'fas fa-motorcycle', 'label' => 'Delivery', 'modulo_id' => 1],
            ['route' => 'admin.repartidores.index', 'icon' => 'fas fa-biking', 'label' => 'Repartidores', 'modulo_id' => 16],
            ['route' => 'clientes.index', 'icon' => 'fas fa-address-book', 'label' => 'Clientes', 'modulo_id' => 15],
        ],
        'Caja' => [
            ['route' => 'admin.caja.index', 'icon' => 'fas fa-cash-register', 'label' => 'Caja', 'modulo_id' => 10],
            ['route' => 'admin.caja.flujo', 'icon' => 'fas fa-money-bill-wave', 'label' => 'Flujo de Caja', 'modulo_id' => 10],
        ]
    ];
    $animationDelayCounter = 0; // Para el efecto cascada
@endphp

        @foreach($menu as $titulo => $items)
            @php
                $mostrarSeccion = collect($items)->contains(fn($item) => auth()->user()->tienePermiso($item['modulo_id'], 'mostrar'));
            @endphp

            @if($mostrarSeccion)
                <div class="space-y-1">
                    {{-- Título de la Sección --}}
                    <div class="px-3 pt-3 pb-1">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] sidebar-text text-slate-400">
                            {{ $titulo }}
                        </span>
                    </div>

                    @foreach($items as $item)
                        @if(auth()->user()->tienePermiso($item['modulo_id'], 'mostrar'))
                            @php
                                try {
                                    $url = route($item['route']);
                                    $isActive = request()->routeIs(str_replace('.index', '.*', $item['route'])) || request()->routeIs($item['route']);
                                } catch (Exception $e) {
                                    $url = '#';
                                    $isActive = false;
                                }
                                $animationDelayCounter += 50; // Incrementar delay en ms
                            @endphp

                            <a href="{{ $url }}" 
                               class="menu-link animate-slide-in relative flex items-center px-3 py-2.5 rounded-xl transition-all duration-300 group overflow-hidden {{ $isActive ? 'bg-blue-50/80 border border-blue-100/50 shadow-sm shadow-blue-900/5' : 'border border-transparent hover:bg-white hover:shadow-sm hover:translate-x-1' }}"
                               style="animation-delay: {{ $animationDelayCounter }}ms;">
                                
                                <div class="menu-icon flex h-9 w-9 items-center justify-center rounded-[10px] transition-all duration-300 shrink-0 relative z-10 {{ $isActive ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-white border border-slate-200/80 text-slate-400 group-hover:text-blue-500 group-hover:border-blue-200 group-hover:bg-blue-50/50' }}">
                                    <i class="{{ $item['icon'] }} text-[14px]"></i>
                                </div>
                                
                                <span class="sidebar-text ml-3 text-[13.5px] tracking-wide {{ $isActive ? 'text-blue-700 font-bold' : 'text-slate-600 font-medium group-hover:text-blue-600' }}">
                                    {{ $item['label'] }}
                                </span>
                                
                                @if($isActive)
                                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-[4px] h-[50%] bg-blue-600 rounded-r-full shadow-[0_0_8px_rgba(37,99,235,0.6)]"></div>
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
    </nav>

    {{-- Footer de Usuario --}}
    <div class="user-footer p-4 mx-3 mb-5 mt-2 rounded-2xl bg-white border border-slate-200/60 flex flex-col gap-3 shrink-0 relative transition-all duration-300 shadow-sm hover:shadow-md">
        <div class="flex items-center w-full">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 text-white flex items-center justify-center font-black text-sm shrink-0 shadow-md mx-auto">
                {{ substr(auth()->user()->nombre ?? 'U', 0, 2) }}
            </div>
            <div class="sidebar-text ml-3 flex flex-col justify-center">
                <p class="text-[13px] font-bold text-slate-900">{{ auth()->user()->nombre ?? 'Usuario' }}</p>
                <p class="text-[10px] text-blue-500 uppercase tracking-widest font-black mt-0.5">{{ optional(auth()->user()->rol)->nombre ?? 'Rol' }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-1 w-full flex justify-center">
            @csrf
            {{-- Botón de logout con gradiente rojo --}}
            <button type="submit" class="btn-logout w-full h-[40px] px-3 flex items-center bg-gradient-to-r from-rose-500 to-red-600 hover:from-red-500 hover:to-rose-600 text-white rounded-xl transition-all duration-300 shadow-md shadow-red-500/20 hover:shadow-red-500/40 active:scale-95 group overflow-hidden border border-red-400/50">
                <div class="flex items-center justify-center shrink-0 w-6">
                    <i class="fas fa-sign-out-alt text-[14px] transition-transform group-hover:translate-x-1"></i>
                </div>
                <span class="sidebar-text ml-2 text-[11px] font-bold tracking-widest mt-[1px]">CERRAR SESIÓN</span>
            </button>
        </form>
    </div>
</aside>

{{-- Fondo oscuro detrás del drawer en móvil --}}
<div id="sidebarOverlay"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleSidebar');
        const navContainer = document.getElementById('nav-container');
        const overlay = document.getElementById('sidebarOverlay');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const esMovil = () => window.matchMedia('(max-width: 1023.98px)').matches;

        if (navContainer) {
            const savedScrollPos = localStorage.getItem('sidebarScrollPosition');
            if (savedScrollPos) {
                navContainer.scrollTop = savedScrollPos;
            }
            navContainer.addEventListener('scroll', () => {
                localStorage.setItem('sidebarScrollPosition', navContainer.scrollTop);
            });
        }

        function abrirDrawerMovil() {
            sidebar.classList.add('abierto');
            overlay.classList.add('visible');
            document.body.style.overflow = 'hidden';
        }

        function cerrarDrawerMovil() {
            sidebar.classList.remove('abierto');
            overlay.classList.remove('visible');
            document.body.style.overflow = '';
        }

        mobileMenuBtn?.addEventListener('click', abrirDrawerMovil);
        overlay?.addEventListener('click', cerrarDrawerMovil);

        document.querySelectorAll('#nav-container .menu-link').forEach(link => {
            link.addEventListener('click', () => {
                if (esMovil()) cerrarDrawerMovil();
            });
        });

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                if (esMovil()) {
                    cerrarDrawerMovil();
                    return;
                }
                sidebar.classList.toggle('colapsado');
                
                if (sidebar.classList.contains('colapsado')) {
                    localStorage.setItem('sidebarState', 'collapsed');
                } else {
                    localStorage.setItem('sidebarState', 'expanded');
                }
            });
        }

        window.addEventListener('resize', () => {
            if (!esMovil()) {
                cerrarDrawerMovil();
            }
        });
    });
</script>