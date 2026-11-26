<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="rounded-md bg-[#90DEC3] px-2.5 py-1 font-mono text-[10px] font-bold tracking-wider text-[#0E241E]">DIREKTORI FASILITAS</span>
            <h1 class="mt-2 text-3xl font-extrabold tracking-[-0.025em]">Pilih Lokasi Fasilitas</h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Cek peta spasial ruang kelompok, Meja Leseh (L), Meja Standar (S), dan Meja Tinggi (H) secara real-time sebelum berangkat.
            </p>
        </div>

        <div class="w-full sm:w-80">
            <input type="text" wire:model.live.debounce.250ms="search"
                placeholder="Cari PUT PNJ, Perpus UI, Depok..."
                class="input-field w-full rounded-xl px-4 py-2.5 text-sm">
        </div>
    </div>

    @if ($activeBooking)
        <div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-500/30 bg-amber-500/12 p-4 dark:bg-amber-950/35">
            <div>
                <p class="text-xs font-bold text-amber-900 dark:text-amber-200">
                    Anda memiliki 1 {{ $activeBooking->status === 'active' ? 'Sesi Aktif' : 'Waiting List' }} di
                    <span class="font-mono">{{ $activeBooking->facilityUnit->code }}</span> ({{ $activeBooking->institution->name }})
                </p>
                <p class="mt-0.5 text-xs text-amber-800 dark:text-amber-300">
                    Slot: <span class="font-mono font-bold">{{ $activeBooking->starts_at->format('H:i') }} - {{ $activeBooking->ends_at->format('H:i') }}</span>.
                    Sesuai aturan anti-monopoli, 1 akun hanya boleh memiliki 1 booking aktif.
                </p>
            </div>
            <a href="{{ route('visitor.session') }}" wire:navigate
                class="btn-brand rounded-lg px-4 py-2 text-xs font-semibold shadow-xs">
                Buka Sesi &amp; Timer &rarr;
            </a>
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        @forelse ($institutions as $inst)
            @php
                $total = $inst->facilityUnits->count();
                $occupied = $inst->facilityUnits->filter(fn ($u) => $u->activeBooking !== null)->count();
                $available = max(0, $total - $occupied);
                $pct = $total > 0 ? round(($available / $total) * 100) : 0;
                $floorsCount = $inst->facilityUnits->pluck('floor')->unique()->count();
            @endphp
            <a href="{{ route('visitor.map', $inst->slug) }}" wire:navigate
                class="surface-card group flex flex-col justify-between rounded-2xl border-t-4 border-t-[#90DEC3] p-6 transition duration-150 hover:-translate-y-0.5 hover:border-[#0F6E56]/55 dark:border-t-[#54C2A0] dark:hover:border-[#90DEC3]/55">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold tracking-tight group-hover:text-[#0F6E56] dark:group-hover:text-[#90DEC3]">{{ $inst->name }}</h2>
                            <p class="mt-1 text-xs font-medium text-[#47625A] dark:text-[#98B4AC]">
                                {{ $inst->building_type }} &middot; {{ $inst->city }}
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            @if ($inst->registration_code)
                                <span class="rounded-md bg-[#90DEC3] px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                                    {{ $inst->registration_code }}
                                </span>
                            @endif
                            @if ($floorsCount > 1)
                                <span class="surface-subtle rounded-md px-2 py-0.5 font-mono text-[10px] font-semibold text-[#47625A] dark:text-[#98B4AC]">
                                    {{ $floorsCount }} Lantai Blueprint
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="mt-3 line-clamp-2 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">{{ $inst->description }}</p>
                </div>

                <div class="mt-5 space-y-3 border-t border-[#CBE8DC] pt-4 text-xs dark:border-[#213531]">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-[#E2F3EC] dark:bg-[#192825]">
                        <div class="h-full rounded-full bg-[#0F6E56] transition-all dark:bg-[#90DEC3]" style="width: {{ $pct }}%"></div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 dark:text-emerald-300">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                {{ $available }} Available
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-semibold text-rose-700 dark:text-rose-300">
                                <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                {{ $occupied }} In Used
                            </span>
                        </div>
                        <span class="font-semibold text-[#0F6E56] dark:text-[#90DEC3]">Buka Denah &rarr;</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="surface-card col-span-full rounded-2xl p-8 text-center text-xs text-[#47625A]">
                Tidak ada instansi yang cocok dengan pencarian Anda.
            </div>
        @endforelse
    </div>
</div>
