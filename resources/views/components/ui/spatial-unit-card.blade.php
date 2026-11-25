@props([
    'unit',
    'selected' => false,
    'waitingCount' => 0,
])

@php
    $active = $unit->activeBooking;
    $status = $unit->currentStatus();

    $cardClasses = match ($status) {
        'available' => 'border-[#D9D1BA] bg-[#FAF7EC] hover:border-[#14452F] hover:bg-[#CAFFA1]/35 dark:border-[#27382F] dark:bg-[#151F1A] dark:hover:border-[#CAFFA1]',
        'warning' => 'border-amber-400 bg-amber-50/90 hover:border-amber-500 dark:border-amber-700 dark:bg-amber-950/40',
        default => 'border-rose-300 bg-rose-50/80 hover:border-[#F43F5E] dark:border-rose-800 dark:bg-rose-950/35',
    };

    $ringClass = $selected
        ? 'ring-2 ring-[#14452F] bg-[#CAFFA1]/60 dark:ring-[#CAFFA1] dark:bg-[#1C2922]'
        : '';
@endphp

<button
    type="button"
    {{ $attributes->merge(['class' => "group relative flex flex-col justify-between rounded-2xl border p-3.5 text-left transition-all duration-150 hover:-translate-y-0.5 {$cardClasses} {$ringClass}"]) }}
>
    <div class="flex items-start justify-between gap-2">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl border border-[#D9D1BA]/80 bg-[#EFEAD8] text-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922] dark:text-[#CAFFA1]">
                @if ($unit->zone === 'group_room')
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <rect x="3" y="4" width="18" height="14" rx="2" />
                        <path d="M7 18v2M17 18v2M8 10h8" stroke-linecap="round" />
                    </svg>
                @elseif ($unit->zone === 'outdoor_desk')
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z" />
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12" stroke-linecap="round" />
                    </svg>
                @elseif ($unit->zone === 'reading_chair')
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M6 19v-8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8M4 14h16" stroke-linecap="round" />
                    </svg>
                @else
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M4 10h16M6 10v9M18 10v9M9 6h6l1 4H8l1-4z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                @endif
            </span>
            <div>
                <div class="flex items-center gap-1.5">
                    <span class="font-mono text-sm font-bold text-[#1A241E] dark:text-[#F4EFE2]">{{ $unit->code }}</span>
                    <span class="font-mono text-[10px] text-[#4E5C52] dark:text-[#9CB09E]">({{ $unit->pos_x }},{{ $unit->pos_y }})</span>
                </div>
                <p class="mt-0.5 text-[11px] font-medium text-[#4E5C52] dark:text-[#B5C4B6] line-clamp-1">{{ $unit->name }}</p>
            </div>
        </div>
        <x-ui.status-badge :status="$status" />
    </div>

    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-[#D9D1BA]/70 pt-2.5 dark:border-[#27382F]">
        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#4E5C52] dark:text-[#B5C4B6]">
            Maks {{ $unit->capacity }} org &middot; {{ $unit->max_duration_minutes }}m
        </span>

        @if ($active)
            <span class="rounded-md bg-rose-200/80 px-2 py-0.5 font-mono text-[10px] font-bold text-rose-900 dark:bg-rose-900/60 dark:text-rose-200">
                Habis pkl {{ $active->ends_at->format('H:i') }}
            </span>
        @else
            <span class="rounded-md bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1A241E] dark:bg-[#1C2922] dark:text-[#CAFFA1]">
                Siap ditempati
            </span>
        @endif
    </div>

    @if ($waitingCount > 0)
        <div class="mt-2 flex items-center gap-1 text-[11px] font-bold text-amber-800 dark:text-amber-300">
            <span class="h-1.5 w-1.5 rounded-full bg-[#F59E0B]"></span>
            {{ $waitingCount }} antrean Waiting List
        </div>
    @endif
</button>
