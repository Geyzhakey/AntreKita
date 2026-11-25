<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="rounded-md bg-[#CAFFA1] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#1A241E]">DIREKTORI FASILITAS</span>
            <h1 class="mt-1.5 font-serif text-3xl font-bold tracking-tight">Pilih Lokasi Fasilitas</h1>
            <p class="mt-1 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">Cek peta spasial ruang kelompok, meja belajar, dan kursi baca secara real-time sebelum berangkat.</p>
        </div>

        <div class="w-full sm:w-72">
            <input type="text" wire:model.live.debounce.250ms="search"
                placeholder="Cari Perpus ITB, UI, PUT PNJ..."
                class="paper-card w-full rounded-xl px-3.5 py-2.5 text-sm outline-none transition focus:border-[#14452F] dark:focus:border-[#CAFFA1]">
        </div>
    </div>

    @if ($activeBooking)
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-300 bg-amber-100/80 p-4 dark:border-amber-700 dark:bg-amber-950/40">
            <div>
                <p class="text-xs font-bold text-amber-950 dark:text-amber-200">
                    Anda memiliki 1 {{ $activeBooking->status === 'active' ? 'Sesi Aktif' : 'Waiting List' }} di
                    <span class="font-mono">{{ $activeBooking->facilityUnit->code }}</span> ({{ $activeBooking->institution->name }})
                </p>
                <p class="mt-0.5 text-xs text-amber-900 dark:text-amber-300">
                    Slot: <span class="font-mono font-bold">{{ $activeBooking->starts_at->format('H:i') }} - {{ $activeBooking->ends_at->format('H:i') }}</span>.
                    Sesuai aturan anti-monopoli, 1 akun hanya boleh memiliki 1 booking aktif.
                </p>
            </div>
            <a href="{{ route('visitor.session') }}" wire:navigate
                class="rounded-xl bg-[#14452F] px-4 py-2 text-xs font-bold text-[#CAFFA1] hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                Buka Sesi &amp; Timer &rarr;
            </a>
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($institutions as $inst)
            @php
                $total = $inst->facilityUnits->count();
                $occupied = $inst->facilityUnits->filter(fn ($u) => $u->activeBooking !== null)->count();
                $available = max(0, $total - $occupied);
                $pct = $total > 0 ? round(($available / $total) * 100) : 0;
            @endphp
            <a href="{{ route('visitor.map', $inst->slug) }}" wire:navigate
                class="paper-card group flex flex-col justify-between rounded-3xl p-5 transition duration-150 hover:-translate-y-0.5 hover:border-[#14452F] dark:hover:border-[#CAFFA1]">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="font-serif text-xl font-bold group-hover:text-[#14452F] dark:group-hover:text-[#CAFFA1]">{{ $inst->name }}</h2>
                        @if ($inst->registration_code)
                            <span class="shrink-0 rounded-md bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1A241E]">
                                {{ $inst->registration_code }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs font-semibold text-[#4E5C52] dark:text-[#9CB09E]">{{ $inst->building_type }} &middot; {{ $inst->city }}</p>
                    <p class="mt-2.5 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6] line-clamp-2">{{ $inst->description }}</p>
                </div>

                <div class="mt-5 space-y-3 border-t border-[#D9D1BA] pt-3.5 text-xs dark:border-[#27382F]">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#EFEAD8] dark:bg-[#1C2922]">
                        <div class="h-full rounded-full bg-[#10B981]" style="width: {{ $pct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 font-bold text-emerald-800 dark:text-emerald-300">
                                <span class="h-2 w-2 rounded-full bg-[#10B981]"></span>
                                {{ $available }} Available
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-rose-800 dark:text-rose-300">
                                <span class="h-2 w-2 rounded-full bg-[#F43F5E]"></span>
                                {{ $occupied }} In Used
                            </span>
                        </div>
                        <span class="font-bold text-[#14452F] dark:text-[#CAFFA1]">Buka Denah &rarr;</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="paper-card col-span-full rounded-2xl p-8 text-center text-xs text-[#4E5C52]">
                Tidak ada instansi yang cocok dengan pencarian Anda.
            </div>
        @endforelse
    </div>
</div>
