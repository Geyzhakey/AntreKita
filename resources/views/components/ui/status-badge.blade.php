@props([
    'status' => 'available',
    'label' => null,
])

@php
    $styles = [
        'available' => [
            'bg' => 'bg-emerald-500/12 text-emerald-800 border-emerald-500/25 dark:bg-emerald-500/15 dark:text-emerald-300 dark:border-emerald-500/30',
            'dot' => 'bg-emerald-500',
            'text' => $label ?? 'Available',
        ],
        'in_used' => [
            'bg' => 'bg-rose-500/12 text-rose-800 border-rose-500/25 dark:bg-rose-500/15 dark:text-rose-300 dark:border-rose-500/30',
            'dot' => 'bg-rose-500',
            'text' => $label ?? 'In Used',
        ],
        'warning' => [
            'bg' => 'bg-amber-500/15 text-amber-900 border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/30',
            'dot' => 'bg-amber-500',
            'text' => $label ?? 'H-5 Menit',
        ],
        'waiting' => [
            'bg' => 'bg-amber-500/15 text-amber-900 border-amber-500/30 dark:bg-amber-500/15 dark:text-amber-300 dark:border-amber-500/30',
            'dot' => 'bg-amber-500',
            'text' => $label ?? 'Waiting List',
        ],
    ];

    $s = $styles[$status] ?? $styles['available'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[10px] font-semibold tracking-tight {$s['bg']}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $s['dot'] }}"></span>
    {{ $s['text'] }}
</span>
