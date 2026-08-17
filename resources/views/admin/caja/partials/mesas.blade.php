{{--
    resources/views/admin/caja/partials/mesas.blade.php

    Tarjetas de las mesas/pedidos con cuenta abierta.

    Vive en un parcial porque lo usan DOS lugares: el render inicial de la
    pantalla y el endpoint admin.caja.api.mesas, que devuelve este mismo HTML
    para el auto-refresco cada 5s. Si estuviera duplicado, cualquier cambio
    de diseño habría que hacerlo en dos sitios y tarde o temprano se
    desincronizan.

    Recibe: $mesas
--}}
@forelse ($mesas as $mesa)
            @php
                $cuenta = $mesa->ordenesActivas->first() ?? null; 
            @endphp

            @if($cuenta)
                {{-- MESA CON ORDEN ACTIVA --}}
                <a href="{{ route('admin.caja.cobrar', $mesa->id) }}" 
                   data-mesa-status="{{ $mesa->estado }}"
                   class="group relative flex flex-col w-full rounded-[1.5rem] sm:rounded-[2rem] border border-slate-200 bg-white shadow-sm hover:shadow-lg hover:border-emerald-200 active:scale-[0.98] cursor-pointer transition-all duration-300 hover:-translate-y-1 overflow-hidden p-4 sm:p-6">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
                    <div class="relative z-10 flex-1 flex flex-col w-full pt-1">
                        <div class="flex justify-between items-start mb-3.5 sm:mb-6 w-full">
                            <div class="w-full">
                               <h3 class="text-base sm:text-2xl font-black tracking-tight text-slate-800 transition-colors group-hover:text-emerald-600 truncate">{{ $mesa->numero }}</h3>
                                @if($mesa->esDelivery())
                                    <p class="inline-flex items-center gap-1 text-[9px] sm:text-[10px] font-black uppercase tracking-wide px-1.5 py-0.5 rounded-md text-white mt-1.5"
                                       style="background-color: {{ $mesa->plataformaDelivery->color ?? '#f97316' }}">
                                        <i class="fas fa-motorcycle"></i> {{ $mesa->plataformaDelivery->nombre ?? 'Delivery' }}
                                    </p>
                                @else
                                    <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest mt-1">Cap. {{ $mesa->capacidad }} p.</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-auto w-full space-y-2 sm:space-y-3">
                            <div class="rounded-xl sm:rounded-2xl px-3 sm:px-4 py-2.5 sm:py-3.5 flex justify-between items-center w-full border border-emerald-100 bg-emerald-50">
                                <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-widest text-emerald-500">Total</span>
                                <p class="text-sm sm:text-xl font-black text-emerald-600">${{ number_format($mesa->total_real ?? 0, 2) }}</p>
                            </div>
                            <div class="w-full py-2.5 sm:py-3.5 flex items-center justify-center gap-1.5 rounded-xl sm:rounded-2xl font-black text-[10px] sm:text-xs uppercase tracking-widest bg-emerald-600 text-white shadow-md shadow-emerald-500/20 transition-all duration-200 group-hover:bg-emerald-700 active:scale-95">
                                 <span>Cobrar</span>
                            </div>
                        </div>
                    </div>
                </a>
            @else
                {{--  MESA SIN ORDEN --}}
                <div data-mesa-status="{{ $mesa->estado }}"
                   class="relative flex flex-col w-full rounded-[1.5rem] sm:rounded-[2rem] border border-slate-200 bg-white shadow-sm overflow-hidden p-4 sm:p-6">
                    <div class="absolute inset-x-0 top-0 h-1 bg-slate-200"></div>
                    <div class="relative z-10 flex-1 flex flex-col w-full pt-1">
                        <div class="flex justify-between items-start mb-3.5 sm:mb-6 w-full">
                            <div class="w-full">
                               <h3 class="text-base sm:text-2xl font-black tracking-tight text-slate-400 truncate">{{ $mesa->numero }}</h3>
                                <p class="text-slate-400 text-[10px] sm:text-xs font-bold uppercase tracking-widest mt-1">Cap. {{ $mesa->capacidad }} p.</p>
                            </div>
                        </div>
                        <div class="mt-auto w-full">
                            <div class="w-full py-2.5 sm:py-3.5 flex items-center justify-center gap-1.5 rounded-xl sm:rounded-2xl font-black text-[9px] sm:text-xs uppercase tracking-widest border border-rose-100 bg-rose-50 text-rose-500 cursor-not-allowed select-none text-center leading-tight px-1">
                                <i class="fas fa-ban"></i> <span class="hidden sm:inline">Mesa sin orden</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-2 lg:col-span-3 xl:col-span-4 flex flex-col items-center justify-center text-center w-full py-14 sm:py-20 bg-white border border-slate-200 rounded-[2rem] shadow-sm">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-50 rounded-[1.5rem] flex items-center justify-center border border-blue-100 shadow-sm mb-4">
                    <i class="fas fa-mug-hot text-2xl sm:text-3xl text-blue-500"></i>
                </div>
                <p class="text-slate-800 font-black text-sm sm:text-base">No hay cuentas abiertas</p>
                <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1.5 max-w-xs">
                    Aquí aparecerán las mesas y los pedidos de delivery en cuanto tengan consumo.
                </p>
            </div>
        @endforelse