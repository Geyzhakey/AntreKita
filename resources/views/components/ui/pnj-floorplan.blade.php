@props([
    'floor' => 1,
    'units',
    'matchingUnitIds' => [],
    'selectedUnitId' => null,
    'clickAction' => 'selectUnit',
])

@php
    $matchingLookup = array_flip($matchingUnitIds);
    $hasFilter = count($matchingUnitIds) < $units->count();
@endphp

<div class="overflow-x-auto pb-2">
    <div class="relative mx-auto min-w-[820px] aspect-[16/10] w-full select-none overflow-hidden rounded-2xl border-2 border-[#0E241E]/70 bg-[#ECF5F1] shadow-inner dark:border-[#263D38] dark:bg-[#0F1917]">
        <!-- Subtle architectural coordinate grid hairline -->
        <div class="pointer-events-none absolute inset-0 opacity-25 dark:opacity-15"
            style="background-image: linear-gradient(to right, rgba(15,110,86,0.14) 1px, transparent 1px), linear-gradient(to bottom, rgba(15,110,86,0.14) 1px, transparent 1px); background-size: 4% 6.4%;">
        </div>

        @if ((int) $floor === 1)
            <!-- FIRST FLOOR ARCHITECTURAL LANDMARKS -->
            <div class="pointer-events-none absolute top-0 left-1/2 z-10 -translate-x-1/2 rounded-b-xl bg-[#0E241E] px-5 py-1 font-mono text-[10px] font-bold tracking-[0.22em] text-[#90DEC3] shadow-xs dark:bg-[#192D28] dark:text-[#90DEC3]">
                FIRST FLOOR &middot; LANTAI 1
            </div>

            <!-- North Compass Top Right -->
            <div class="pointer-events-none absolute top-2.5 right-3 z-10 flex flex-col items-center font-mono text-[9px] font-bold text-[#0E241E]/75 dark:text-[#98B4AC]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l4.5 11-4.5-2.5L7.5 13 12 2z" />
                </svg>
                <span>N</span>
            </div>

            <!-- Top-right architectural wall recess -->
            <div class="pointer-events-none absolute top-0 right-[9%] h-[4.5%] w-[18%] rounded-b-lg border-x border-b border-[#0E241E]/30 bg-[#CBE8DC]/70 dark:border-[#263D38] dark:bg-[#192825]"></div>

            <!-- Corner greenery dots matching original architectural plan -->
            <div class="pointer-events-none absolute top-[6.5%] left-[25.5%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>
            <div class="pointer-events-none absolute top-[6.5%] right-[28%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>
            <div class="pointer-events-none absolute bottom-[6%] left-[5.5%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>
            <div class="pointer-events-none absolute right-[4%] bottom-[6%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>

            <!-- MEJA LESEH Zone Tag below L1 & L2 -->
            <div class="pointer-events-none absolute top-[41.8%] left-[4.2%] flex w-[15.2%] items-center justify-center rounded-lg border border-amber-700/20 bg-[#E6D6BC]/80 py-1 font-mono text-[9px] font-bold tracking-wider text-[#5A4732] dark:border-[#263D38] dark:bg-[#192825] dark:text-[#90DEC3]">
                MEJA LESEH
            </div>
        @else
            <!-- SECOND FLOOR ARCHITECTURAL LANDMARKS -->
            <div class="pointer-events-none absolute top-0 right-10 z-10 rounded-b-xl bg-[#0E241E] px-4 py-1 font-mono text-[10px] font-bold tracking-[0.2em] text-[#90DEC3] shadow-xs dark:bg-[#192D28] dark:text-[#90DEC3]">
                SECOND FLOOR &middot; LANTAI 2
            </div>

            <!-- Ruang Fokus Architectural Enclosure -->
            <div class="pointer-events-none absolute top-[2.2%] left-[11.2%] h-[24.5%] w-[31.2%] rounded-xl border-2 border-[#0E241E]/45 bg-[#E2EFEA]/75 dark:border-[#2E4B44] dark:bg-[#15221F]/75">
                <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 font-sans text-xs font-bold tracking-tight text-[#0E241E]/85 dark:text-[#E6F2EE]">
                    Ruang Fokus
                </span>
            </div>

            <!-- Center Stairwell ("tangga ↑") -->
            <div class="pointer-events-none absolute top-[34.5%] left-[39.2%] flex h-[32.5%] w-[21.4%] flex-col items-center justify-center overflow-hidden rounded-lg border-2 border-[#0E241E]/60 bg-[#DCEBE5] shadow-xs dark:border-[#2E4B44] dark:bg-[#162420]">
                <div class="absolute inset-y-2 left-2 w-[38%] rounded-xs border border-[#0E241E]/20 bg-[#CBE0D7]/70 dark:border-[#2E4B44] dark:bg-[#1C2E29]"
                    style="background-image: repeating-linear-gradient(to bottom, transparent 0 8px, rgba(14,36,30,0.22) 8px 9.5px);">
                </div>
                <div class="relative z-10 ml-12 flex flex-col items-center text-[#0E241E] dark:text-[#90DEC3]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M12 19V5M6 11l6-6 6 6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span class="mt-0.5 font-mono text-[11px] font-bold tracking-tight">tangga</span>
                </div>
            </div>

            <!-- Corner greenery accents -->
            <div class="pointer-events-none absolute top-[7.5%] right-[5%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>
            <div class="pointer-events-none absolute bottom-[4.5%] left-[10.8%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>
            <div class="pointer-events-none absolute right-[7%] bottom-[4.5%] flex h-6 w-6 items-center justify-center rounded-full bg-[#0F6E56]/15 text-[#0F6E56] dark:bg-[#90DEC3]/10 dark:text-[#90DEC3]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c2 4 6 5 6 10a6 6 0 0 1-12 0c0-5 4-6 6-10z"/></svg>
            </div>
        @endif

        <!-- INTERACTIVE FACILITY UNITS -->
        @foreach ($units as $unit)
            @if ($unit->map_x !== null && $unit->map_y !== null)
                @php
                    $status = $unit->currentStatus();
                    $waitingCount = $unit->waitingBookings->count();
                    $isSelected = (int) $selectedUnitId === $unit->id;
                    $isMatched = isset($matchingLookup[$unit->id]);
                    $isRoom = $unit->seat_type === 'ROOM';
                    $isSofa = $unit->seat_type === 'SOFA';
                    $isLeseh = $unit->seat_type === 'L';
                    $isHigh = $unit->seat_type === 'H';

                    $stateStyles = match ($status) {
                        'in_used' => 'border-rose-500/80 bg-rose-100/95 text-rose-950 hover:bg-rose-200/95 dark:border-rose-500/70 dark:bg-rose-950/85 dark:text-rose-100',
                        'warning' => 'border-amber-500 bg-amber-100/95 text-amber-950 hover:bg-amber-200 dark:border-amber-400 dark:bg-amber-950/85 dark:text-amber-100',
                        default => match ($unit->seat_type) {
                            'ROOM' => 'border-[#0F6E56]/40 bg-[#0F6E56] text-[#F7FCFA] hover:bg-[#0B5643] dark:border-[#2E4B44] dark:bg-[#1D352E] dark:text-[#E6F2EE] dark:hover:bg-[#24433B]',
                            'L' => 'border-amber-800/35 bg-[#E6C596] text-[#2A1F12] hover:bg-[#DFB982] dark:border-[#90DEC3]/35 dark:bg-[#192D28] dark:text-[#90DEC3]',
                            'H' => 'border-stone-600/45 bg-[#D9BC91] text-[#261D12] hover:bg-[#CEAE7E] dark:border-teal-500/40 dark:bg-[#17282A] dark:text-[#90DEC3]',
                            default => 'border-[#655440]/35 bg-[#E8BF8E] text-[#261C12] hover:bg-[#DFB077] dark:border-[#2E4B44] dark:bg-[#182924] dark:text-[#E6F2EE]',
                        },
                    };

                    $dimClass = ($hasFilter && ! $isMatched && ! $isSelected) ? 'opacity-20 grayscale' : 'opacity-100';
                    $selectedRing = $isSelected
                        ? 'z-30 scale-[1.06] ring-3 ring-[#0F6E56] shadow-xl dark:ring-[#90DEC3]'
                        : 'z-10 hover:z-20 hover:scale-[1.04] hover:shadow-md';
                @endphp

                <button
                    type="button"
                    wire:key="pnj-unit-{{ $unit->id }}"
                    wire:click="{{ $clickAction }}({{ $unit->id }})"
                    style="left: {{ $unit->map_x }}%; top: {{ $unit->map_y }}%; width: {{ $unit->map_w }}%; height: {{ $unit->map_h }}%;"
                    class="group absolute flex flex-col items-center justify-center border transition-all duration-150 focus:outline-none {{ $isRoom ? 'rounded-xl' : ($isHigh ? 'rounded-md' : 'rounded-lg') }} {{ $stateStyles }} {{ $selectedRing }} {{ $dimClass }}"
                >
                    @if ($status === 'in_used')
                        <span class="absolute -top-1 -left-1 h-2.5 w-2.5 rounded-full border border-[#F7FCFA] bg-[#F43F5E] shadow-xs dark:border-[#0F1917]"></span>
                    @elseif ($status === 'warning')
                        <span class="absolute -top-1 -left-1 h-2.5 w-2.5 animate-ping rounded-full bg-amber-500"></span>
                        <span class="absolute -top-1 -left-1 h-2.5 w-2.5 rounded-full border border-[#F7FCFA] bg-amber-500"></span>
                    @endif

                    @if ($waitingCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 z-10 rounded-full bg-[#0E241E] px-1.5 py-0.5 font-mono text-[8px] font-bold leading-none text-[#90DEC3] shadow-xs dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                            +{{ $waitingCount }}
                        </span>
                    @endif

                    @if ($isRoom)
                        <div class="flex flex-col items-center justify-center px-1 text-center">
                            @if (str_contains($unit->code, 'PODCAST'))
                                <svg class="mb-1 h-4 w-4 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2M12 19v3M8 22h8" stroke-linecap="round"/></svg>
                            @elseif (str_contains($unit->code, 'PRANCIS'))
                                <svg class="mb-1 h-5 w-5 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M4 18h16M6 18V9M10 18V9M14 18V9M18 18V9M2 9l10-6 10 6H2z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @elseif (str_contains($unit->code, 'PUSTAKA'))
                                <svg class="mb-0.5 h-4 w-4 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 6s4-2 10 0c6-2 10 0 10 0v13s-4-2-10 0c-6-2-10 0-10 0V6zM12 6v13" stroke-linecap="round"/></svg>
                            @elseif (str_contains($unit->code, 'INTERAKTIF'))
                                <svg class="mb-0.5 h-4 w-4 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="7" width="20" height="10" rx="4"/><path d="M7 12h4M9 10v4M15 11h.01M17 13h.01" stroke-linecap="round"/></svg>
                            @elseif (str_contains($unit->code, 'SINERGI'))
                                <svg class="mb-0.5 h-4 w-4 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke-linecap="round"/></svg>
                            @elseif (str_contains($unit->code, 'LAYAR'))
                                <svg class="mb-0.5 h-4 w-4 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="14" rx="2"/><path d="M10 9l5 2.5-5 2.5V9zM8 21h8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                <svg class="mb-0.5 h-4 w-4 opacity-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            @endif

                            <span class="text-[9px] font-bold leading-tight tracking-tight uppercase">
                                {{ $unit->shortLabel() }}
                            </span>
                            <span class="mt-0.5 rounded-full px-1.5 py-0.2 font-mono text-[8px] font-bold {{ $status === 'available' ? 'bg-[#90DEC3] text-[#0E241E]' : 'bg-white/25' }}">
                                {{ $unit->capacity }} org
                            </span>
                        </div>
                    @elseif ($isSofa)
                        <span class="-rotate-90 font-mono text-[10px] font-bold tracking-[0.22em]">SOFA</span>
                    @else
                        @if ($isHigh)
                            <span class="pointer-events-none absolute -top-1 left-1/2 h-1 w-2.5 -translate-x-1/2 rounded-full bg-current opacity-35"></span>
                            <span class="pointer-events-none absolute -bottom-1 left-1/2 h-1 w-2.5 -translate-x-1/2 rounded-full bg-current opacity-35"></span>
                        @elseif ($isLeseh)
                            <span class="pointer-events-none absolute inset-0.5 rounded-[5px] border border-dashed border-current/25"></span>
                        @endif

                        <span class="font-mono {{ strlen($unit->shortLabel()) > 2 ? 'text-[9px]' : 'text-[10px]' }} font-extrabold leading-none tracking-tight">
                            {{ $unit->shortLabel() }}
                        </span>
                    @endif

                    <!-- Hover Tooltip -->
                    <div class="pointer-events-none absolute bottom-full left-1/2 z-50 mb-2 hidden w-44 -translate-x-1/2 rounded-xl border border-[#263D38] bg-[#0E241E]/95 p-2.5 text-left text-[#EEF8F4] shadow-xl backdrop-blur-md group-hover:block">
                        <div class="flex items-center justify-between gap-1">
                            <span class="font-mono text-[10px] font-bold text-[#90DEC3]">{{ $unit->code }}</span>
                            <span class="rounded bg-white/15 px-1.5 py-0.5 font-mono text-[9px] font-bold">
                                {{ $status === 'available' ? 'Tersedia' : ($status === 'warning' ? 'H-5 Menit' : 'Terisi') }}
                            </span>
                        </div>
                        <p class="mt-1 text-[10px] font-semibold leading-snug">{{ $unit->name }}</p>
                        <p class="mt-0.5 text-[9px] text-[#98B4AC]">
                            Tipe: <span class="font-bold text-[#EEF8F4]">{{ $unit->seatTypeLabel() }}</span> &middot; Maks {{ $unit->capacity }} org
                        </p>
                    </div>
                </button>
            @endif
        @endforeach
    </div>
</div>
