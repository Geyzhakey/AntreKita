@props([
    'status' => 'available',
    'label' => null,
])

@php
    $styles = [
        'available' => [
            'bg' => 'bg-emerald-100/90 text-emerald-900 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-700',
            'dot' => 'bg-[#10B981]',
            'text' => $label ?? 'Available',
        ],
        'in_used' => [
            'bg' => 'bg-rose-100/90 text-rose-900 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-700',
            'dot' => 'bg-[#F43F5E]',
            'text' => $label ?? 'In Used',
        ],
        'warning' => [
            'bg' => 'bg-amber-100/90 text-amber-950 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700',
            'dot' => 'bg-[#F59E0B]',
            'text' => $label ?? 'H-5 Menit',
        ],
        'waiting' => [
            'bg' => 'bg-amber-100/90 text-amber-950 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-700',
            'dot' => 'bg-[#F59E0B]',
            'text' => $label ?? 'Waiting List',
        ],
    ];

    $s = $styles[$status] ?? $styles['available'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-[10px] font-bold tracking-tight {$s['bg']}"]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $s['dot'] }}"></span>
    {{ $s['text'] }}
</span>
