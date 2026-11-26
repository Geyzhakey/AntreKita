@props([
    'unit',
    'selected' => false,
    'waitingCount' => 0,
])

@php
    $active = $unit->activeBooking;
    $status = $unit->currentStatus();

    $cardClasses = match ($status) {
        'available' => 'border-[#CBE8DC] bg-[#F7FCFA] hover:border-[#0F6E56]/55 dark:border-[#213531] dark:bg-[#131F1D] dark:hover:border-[#90DEC3]/55',
        'warning' => 'border-amber-400/70 bg-amber-50/85 hover:border-amber-500 dark:border-amber-600/50 dark:bg-amber-950/30',
        default => 'border-rose-300/80 bg-rose-50/80 hover:border-rose-500 dark:border-rose-800/60 dark:bg-rose-950/25',
    };

    $ringClass = $selected
        ? 'ring-2 ring-[#0F6E56] border-[#0F6E56] bg-[#90DEC3]/25 dark:ring-[#90DEC3] dark:border-[#90DEC3] dark:bg-[#192825]'
        : '';
@endphp

<button
    type="button"
    {{ $attributes->merge(['class' => "group relative flex flex-col justify-between rounded-2xl border p-3.5 text-left transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md {$cardClasses} {$ringClass}"]) }}
>
    <div class="flex items-start justify-between gap-2">
        <div class="flex items-start gap-2.5">
            <span class="surface-subtle mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-[#0F6E56] dark:text-[#90DEC3]">
                @if ($unit->seat_type === 'ROOM' || $unit->zone === 'group_room')
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="4" width="18" height="14" rx="2" />
                        <path d="M7 18v2M17 18v2M8 10h8" stroke-linecap="round" />
                    </svg>
                @elseif ($unit->seat_type === 'L')
                    <span class="font-mono text-xs font-extrabold">L</span>
                @elseif ($unit->seat_type === 'H')
                    <span class="font-mono text-xs font-extrabold">H</span>
                @elseif ($unit->seat_type === 'F')
                    <span class="font-mono text-xs font-extrabold">F</span>
                @elseif ($unit->seat_type === 'SOFA')
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 11V7a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v4M3 11a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z" stroke-linecap="round" />
                    </svg>
                @else
                    <span class="font-mono text-xs font-extrabold">S</span>
                @endif
            </span>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="font-mono text-sm font-bold tracking-tight text-[#0E241E] dark:text-[#E6F2EE]">{{ $unit->code }}</span>
                    @if ($unit->floor)
                        <span class="rounded bg-[#E2F3EC] px-1.5 py-0.5 font-mono text-[9px] font-bold text-[#47625A] dark:bg-[#192825] dark:text-[#98B4AC]">
                            Lt.{{ $unit->floor }}
                        </span>
                    @endif
                </div>
                <p class="mt-0.5 line-clamp-1 text-[11px] font-medium text-[#47625A] dark:text-[#98B4AC]">{{ $unit->name }}</p>
            </div>
        </div>
        <x-ui.status-badge :status="$status" />
    </div>

    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-[#CBE8DC]/70 pt-2.5 dark:border-[#213531]">
        <span class="text-[11px] font-medium text-[#47625A] dark:text-[#98B4AC]">
            {{ $unit->seatTypeLabel() }} &middot; {{ $unit->capacity }} org
        </span>

        @if ($active)
            <span class="rounded-md bg-rose-500/15 px-2 py-0.5 font-mono text-[10px] font-bold text-rose-700 dark:text-rose-300">
                s/d {{ $active->ends_at->format('H:i') }}
            </span>
        @else
            <span class="rounded-md bg-[#90DEC3]/60 px-2 py-0.5 font-mono text-[10px] font-bold text-[#0E241E] dark:bg-[#192D28] dark:text-[#90DEC3]">
                Siap ditempati
            </span>
        @endif
    </div>

    @if ($waitingCount > 0)
        <div class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold text-amber-700 dark:text-amber-300">
            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
            {{ $waitingCount }} antrean Waiting List
        </div>
    @endif
</button>
