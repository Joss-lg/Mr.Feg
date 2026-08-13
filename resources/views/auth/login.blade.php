<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Mr. Feg - Acceso</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mrlogo.png') }}?v=2">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        :root {
            /* Acentos Naranja de Mr. Feg */
            --coral-400: #fb923c; 
            --coral-500: #f97316; 
            --coral-700: #c2410c; 
            
            --text-color: #F8FAFC;
            
            /* Fondo Modo Oscuro (Degradado Azul) */
            --bg-color: #0a4670;
            --bg-image: linear-gradient(to bottom, #38b6ff 0%, #1573b0 50%, #0a4670 100%);
            --titulo-color: #ffffff;
        }

        /* Variables Modo Claro (#A5B4FC) */
        body.modo-claro {
            --bg-color: #A5B4FC; 
            --bg-image: none;    
            --titulo-color: #020617;
        }

        html, body {
            height: 100%;
        }

        body { 
            background-color: var(--bg-color);
            background-image: var(--bg-image);
            color: var(--text-color); 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            padding: 0; 
            min-height: 100vh;
            min-height: 100dvh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            transition: background-color 0.4s ease, color 0.4s ease;
        }

        .logo-box {
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        /* VISOR EN NEGRO PROFUNDO */
        .visor-screen {
            background-color: #0a0a0a; 
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: inset 0 4px 15px rgba(0, 0, 0, 0.8); 
        }

        /* BOTONES EN NEGRO NEUTRO */
        .key-btn {
            background-color: #111111;
            color: var(--text-color);
            border: 1px solid #262626;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.25);
            transition: all 0.15s ease-in-out;
        }

        .key-btn:hover { 
            transform: translateY(-2px); 
            background-color: #1a1a1a;
            border-color: #333333; 
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4);
        }
        
        .key-btn:active { 
            transform: scale(0.96); 
            background-color: #000000; 
            border-color: #000000;
            box-shadow: inset 0 2px 6px rgba(255, 255, 255, 0.1); 
        }

        .btn-ok {
            background: linear-gradient(180deg, var(--coral-400) 0%, var(--coral-500) 100%);
            border: none;
            border-bottom: 4px solid var(--coral-700); 
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.4);
            color: white !important;
            transition: all 0.1s ease-in-out;
        }

        .btn-ok:active {
            border-bottom-width: 0px;
            transform: translateY(4px) scale(0.96);
        }

        .cursor-blink { animation: blink 1s infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

        @media (max-height: 700px) {
            .compact-header { margin-bottom: 0.75rem !important; }
            .compact-header img { width: 4.5rem !important; height: 4.5rem !important; }
            .compact-header h1 { font-size: 1.15rem !important; }
            .compact-header p { margin-top: 0.15rem !important; }
            .compact-visor { height: 3rem !important; margin-bottom: 0.75rem !important; }
            .compact-visor span:first-child { font-size: 1.5rem !important; }
            .keypad-grid { gap: 0.5rem !important; }
        }

        /* =========================================
           INTERRUPTOR DE CÁPSULA (COLORES MR. FEG)
           ========================================= */
        .capsule-wrapper {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 150;
        }

        .theme-toggle-input {
            display: none;
        }

        .capsule-track {
            position: relative;
            display: block;
            width: 82px;
            height: 38px;
            /* NIGHTMODE: Fondo Negro igual al teclado */
            background: #111111;
            border: 1px solid #262626;
            border-radius: 40px;
            cursor: pointer;
            box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.6), 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: background 0.5s ease, border-color 0.5s ease;
        }

        .capsule-thumb {
            position: absolute;
            top: 2px;
            left: 46px; /* Lado derecho por defecto (Noche) */
            width: 32px;
            height: 32px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .capsule-icon {
            position: absolute;
            width: 18px;
            height: 18px;
            transition: opacity 0.4s ease, transform 0.5s ease;
        }

        .icon-sun-svg {
            opacity: 0;
            transform: rotate(-90deg);
            color: var(--coral-500); /* Sol Naranja */
        }

        .icon-moon-svg {
            opacity: 1;
            transform: rotate(0deg);
            color: #111111; /* Luna Negra */
        }

        /* --- ESTADO MODO CLARO (CHECKED - DAYMODE) --- */
        .theme-toggle-input:checked + .capsule-track {
            /* DAYMODE: Degradado Naranja igual al botón OK */
            background: linear-gradient(to right, var(--coral-400), var(--coral-500));
            border-color: transparent;
            box-shadow: inset 0 2px 6px rgba(200, 50, 0, 0.3), 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .theme-toggle-input:checked + .capsule-track .capsule-thumb {
            transform: translateX(-44px); /* Desplaza la esfera a la izquierda */
        }

        .theme-toggle-input:checked + .capsule-track .icon-sun-svg {
            opacity: 1;
            transform: rotate(0deg);
        }

        .theme-toggle-input:checked + .capsule-track .icon-moon-svg {
            opacity: 0;
            transform: rotate(90deg);
        }
    </style>
</head>
<body class="w-full flex flex-col items-center justify-center relative p-4" style="padding-top: max(1rem, env(safe-area-inset-top)); padding-bottom: max(1rem, env(safe-area-inset-bottom));">

    <!-- Script de precarga de estado para evitar parpadeos -->
    <script>
        const temaGuardado = localStorage.getItem('tema-mrfeg');
        if (temaGuardado === 'claro') {
            document.body.classList.add('modo-claro');
        }
    </script>

    <!-- Interruptor Estilo Cápsula Día/Noche -->
    <div class="capsule-wrapper">
        <input type="checkbox" id="themeSwitch" class="theme-toggle-input" onchange="toggleTheme(this.checked)">
        <label for="themeSwitch" class="capsule-track" title="Cambiar tema Día / Noche">
            <div class="capsule-thumb">
                <!-- Ícono Sol (DAYMODE) -->
                <svg class="capsule-icon icon-sun-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                </svg>
                <!-- Ícono Luna y Estrellas (NIGHTMODE) -->
                <svg class="capsule-icon icon-moon-svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3a9 9 0 1 0 9 9c0-.46-.04-.92-.1-1.36a5.389 5.389 0 0 1-4.4 2.26 5.403 5.403 0 0 1-5.4-5.4c0-1.81 1-3.39 2.47-4.22-.44-.06-.9-.1-1.36-.1z"/>
                    <path d="M19 3l.6 1.4.8.6-1.4.6-.6 1.4-.6-1.4-1.4-.6 1.4-.6.6-1.4zM21 9l.3.7.7.3-.7.3-.3.7-.3-.7-.7-.3.7-.3.3-.7z"/>
                </svg>
            </div>
        </label>
    </div>

    <div class="w-full max-w-[320px] sm:max-w-[350px] flex flex-col items-center my-auto py-2">
        
        {{-- Cabecera --}}
        <div class="compact-header flex flex-col items-center mb-8 sm:mb-10">
            <div class="logo-box p-2 rounded-xl mb-4">
                <img src="{{ asset('images/mrlogo.png') }}" alt="Logo Mr. Feg" class="mx-auto w-20 h-20 sm:w-24 sm:h-24 object-contain">
            </div>
            
            <h1 class="text-xl sm:text-2xl font-black tracking-widest leading-none uppercase drop-shadow-md text-[var(--titulo-color)] transition-colors duration-400">
                Mr. Feg
            </h1>
            <p class="text-[9px] sm:text-[10px] uppercase tracking-[0.4em] font-bold mt-2 text-orange-400 drop-shadow-sm">Restaurante</p>
        </div>

        @if($errors->any())
            <div class="w-full bg-orange-500/20 border border-orange-500/30 py-2 sm:py-3 rounded-xl mb-4 flex items-center justify-center">
                <p class="text-orange-500 text-[10px] font-black uppercase tracking-widest">NIP Incorrecto</p>
            </div>
        @endif

        <form action="{{ route('login.pin') }}" method="POST" id="pinForm" class="hidden">
            @csrf
            <input type="password" name="codigo_empleado" id="pinHidden">
        </form>

        {{-- Visor del PIN --}}
        <div class="compact-visor w-full h-14 sm:h-16 visor-screen rounded-2xl mb-5 sm:mb-6 flex items-center justify-center gap-2 relative overflow-hidden">
            <span id="pinDisplay" class="text-3xl sm:text-4xl font-black tracking-[0.4em] text-white mt-1"></span>
            <span class="cursor-blink w-[2px] h-8 rounded-full" style="background-color: var(--coral-400);"></span>
        </div>

        {{-- Teclado Numérico --}}
        <div class="keypad-grid grid grid-cols-4 gap-3 sm:gap-4 w-full">
            <button type="button" onclick="appendNumber('1')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">1</button>
            <button type="button" onclick="appendNumber('2')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">2</button>
            <button type="button" onclick="appendNumber('3')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">3</button>
            
            {{-- Botón OK Naranja --}}
            <button type="button" onclick="submitForm()" class="btn-ok col-span-1 row-span-2 rounded-2xl flex flex-col items-center justify-center w-full h-full">
                <span class="font-black text-2xl sm:text-3xl leading-tight drop-shadow-md">OK</span>
                <span class="text-[8px] sm:text-[9px] font-bold uppercase mt-1 opacity-90 tracking-wider">Entrar</span>
            </button>

            <button type="button" onclick="appendNumber('4')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">4</button>
            <button type="button" onclick="appendNumber('5')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">5</button>
            <button type="button" onclick="appendNumber('6')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">6</button>

            <button type="button" onclick="appendNumber('7')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">7</button>
            <button type="button" onclick="appendNumber('8')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">8</button>
            <button type="button" onclick="appendNumber('9')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">9</button>
            
            <button type="button" onclick="deleteNumber()" class="key-btn aspect-square rounded-2xl flex flex-col items-center justify-center">
                <i class="fas fa-backspace text-orange-400 text-lg sm:text-xl"></i>
            </button>

            <div class="col-span-1 aspect-square"></div>
            <button type="button" onclick="appendNumber('0')" class="key-btn col-span-2 rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center w-full h-full">0</button>
            <div class="col-span-1"></div>
        </div>
    </div>

    <script>
        // --- LÓGICA DEL TECLADO ---
        const pinHidden = document.getElementById('pinHidden');
        const pinDisplay = document.getElementById('pinDisplay');
        const pinForm = document.getElementById('pinForm');

        function updateDisplay() {
            if (navigator.vibrate) navigator.vibrate(10); 
            pinDisplay.textContent = '•'.repeat(pinHidden.value.length);
        }

        function appendNumber(number) {
            if (pinHidden.value.length < 8) { 
                pinHidden.value += number;
                updateDisplay();
            }
        }

        function deleteNumber() {
            pinHidden.value = pinHidden.value.slice(0, -1);
            updateDisplay();
        }

        function submitForm() {
            if (pinHidden.value.length >= 2) pinForm.submit();
        }

        document.addEventListener('keydown', (e) => {
            if (e.key >= '0' && e.key <= '9') appendNumber(e.key);
            if (e.key === 'Backspace') deleteNumber();
            if (e.key === 'Enter') submitForm();
        });

        // --- LÓGICA DEL INTERRUPTOR CÁPSULA Y TEMA ---
        const themeSwitch = document.getElementById('themeSwitch');

        function toggleTheme(esClaro) {
            const body = document.body;
            if (esClaro) {
                body.classList.add('modo-claro');
                localStorage.setItem('tema-mrfeg', 'claro');
            } else {
                body.classList.remove('modo-claro');
                localStorage.setItem('tema-mrfeg', 'oscuro');
            }
        }

        // Sincronizar el checkbox al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const esClaro = document.body.classList.contains('modo-claro');
            if (themeSwitch) {
                themeSwitch.checked = esClaro;
            }
        });
    </script>
</body>
</html>