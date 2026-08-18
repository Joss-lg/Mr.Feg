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
            --text-color: #1e293b;
            --bg-color: #F2F2F2; /* Color de fondo actualizado a #F2F2F2 */
            --titulo-color: #1e293b;
        }

        /* Variables Modo Alternativo / Oscuro */
        body.modo-claro {
            --bg-color: #0f172a; 
            --text-color: #f8fafc;
            --titulo-color: #ffffff;
        }

        html, body {
            height: 100%;
        }

        body { 
            background-color: var(--bg-color);
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
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        /* VISOR CON EL GRIS DE MODALES (#f8fafc / slate-50) */
        .visor-screen {
            background-color: #f8fafc; 
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03); 
        }

        /* BOTONES CON EL GRIS SUAVE DE MODALES */
        .key-btn {
            background-color: #f8fafc;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
            transition: all 0.15s ease-in-out;
        }

        .key-btn:hover { 
            transform: translateY(-2px); 
            background-color: #ffffff;
            border-color: #cbd5e1; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }
        
        .key-btn:active { 
            transform: scale(0.96); 
            background-color: #e2e8f0; 
            border-color: #cbd5e1;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05); 
        }

        .btn-ok {
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            border-bottom: 4px solid #1d4ed8; 
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
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

    <div class="w-full max-w-[320px] sm:max-w-[350px] flex flex-col items-center my-auto py-2">
        
        {{-- Cabecera --}}
        <div class="compact-header flex flex-col items-center mb-8 sm:mb-10">
            <div class="logo-box p-3 rounded-[2rem] mb-4 shadow-sm">
                <img src="{{ asset('images/mrlogo.png') }}" alt="Logo Mr. Feg" class="mx-auto w-20 h-20 sm:w-24 sm:h-24 object-contain">
            </div>
            
            <h1 class="text-xl sm:text-2xl font-black tracking-widest leading-none uppercase drop-shadow-sm text-[var(--titulo-color)] transition-colors duration-400">
                Mr. Feg
            </h1>
            <p class="text-[9px] sm:text-[10px] uppercase tracking-[0.4em] font-black mt-2 text-blue-600">Restaurante</p>
        </div>

        @if($errors->any())
            <div class="w-full bg-rose-50 border border-rose-200 py-2.5 sm:py-3 rounded-2xl mb-4 flex items-center justify-center shadow-sm">
                <p class="text-rose-600 text-[10px] font-black uppercase tracking-widest">NIP Incorrecto</p>
            </div>
        @endif

        <form action="{{ route('login.pin') }}" method="POST" id="pinForm" class="hidden">
            @csrf
            <input type="password" name="codigo_empleado" id="pinHidden">
        </form>

        {{-- Visor del PIN --}}
        <div class="compact-visor w-full h-14 sm:h-16 visor-screen rounded-2xl mb-5 sm:mb-6 flex items-center justify-center gap-2 relative overflow-hidden shadow-sm">
            <span id="pinDisplay" class="text-3xl sm:text-4xl font-black tracking-[0.4em] text-slate-800 mt-1"></span>
            <span class="cursor-blink w-[2px] h-8 rounded-full bg-blue-600"></span>
        </div>

        {{-- Teclado Numérico --}}
        <div class="keypad-grid grid grid-cols-4 gap-3 sm:gap-4 w-full">
            <button type="button" onclick="appendNumber('1')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">1</button>
            <button type="button" onclick="appendNumber('2')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">2</button>
            <button type="button" onclick="appendNumber('3')" class="key-btn aspect-square rounded-2xl text-2xl sm:text-3xl font-bold flex items-center justify-center">3</button>
            
            {{-- Botón OK Azul Principal --}}
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
                <i class="fas fa-backspace text-blue-600 text-lg sm:text-xl"></i>
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
    </script>
</body>
</html>