@php
    $currentUser = auth()->user();
    $totalMitra = $institutions->count();
    $totalUnitsAll = $institutions->sum(fn ($i) => $i->facilityUnits->count());
    $totalAvailableAll = $institutions->sum(fn ($i) => $i->facilityUnits->filter(fn ($u) => $u->activeBooking === null)->count());
@endphp

<div class="space-y-6">
    <!-- Hero / Welcome Card after Visitor Sign-In -->
    <section class="surface-card ak-fade-up relative overflow-hidden rounded-3xl border-t-4 border-t-[#0F6E56] p-6 sm:p-7 dark:border-t-[#90DEC3]">
        <div class="pointer-events-none absolute -top-16 -right-16 h-48 w-48 rounded-full bg-[#90DEC3]/25 blur-2xl dark:bg-[#90DEC3]/10"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2.5">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-md bg-[#90DEC3] px-2.5 py-1 font-mono text-[10px] font-bold tracking-wider text-[#0E241E]">
                        <span class="ak-pulse-dot h-2 w-2 rounded-full bg-[#0F6E56]"></span>
                        PORTAL PENGUNJUNG AKTIF
                    </span>
                    @if ($currentUser?->affiliation)
                        <span class="surface-subtle rounded-md px-2.5 py-1 font-mono text-[10px] font-semibold text-[#47625A] dark:text-[#98B4AC]">
                            {{ $currentUser->affiliation }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-extrabold tracking-[-0.025em] sm:text-3xl">
                    Halo, {{ $currentUser?->name ?? 'Pengunjung' }}!
                </h1>
                <p class="max-w-2xl text-xs leading-relaxed text-[#47625A] sm:text-sm dark:text-[#98B4AC]">
                    Pilih gedung atau perpustakaan mitra di bawah ini untuk memantau ketersediaan meja &amp; ruangan secara spasial real-time sebelum berangkat.
                </p>
            </div>

            <div class="w-full shrink-0 lg:w-84">
                <label for="search-facility" class="sr-only">Cari Lokasi Fasilitas</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-[#47625A] dark:text-[#98B4AC]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m20 20-3.5-3.5" stroke-linecap="round" />
                    </svg>
                    <input id="search-facility" type="text" wire:model.live.debounce.250ms="search"
                        placeholder="Cari PUT PNJ, Depok, atau lokasi mitra..."
                        class="input-field w-full rounded-xl py-2.5 pr-4 pl-10 text-sm">
                </div>
            </div>
        </div>

        <!-- 3 Quick Summary Stat Pills -->
        <div class="relative mt-5 grid gap-3 border-t border-[#CBE8DC]/80 pt-4 sm:grid-cols-3 dark:border-[#213531]">
            <div class="surface-subtle flex items-center justify-between rounded-xl px-4 py-2.5">
                <span class="text-[11px] font-semibold text-[#47625A] dark:text-[#98B4AC]">Mitra Gedung Aktif</span>
                <span class="font-mono text-sm font-extrabold text-[#0E241E] dark:text-[#E6F2EE]">{{ $totalMitra }} Lokasi</span>
            </div>

            <div class="surface-subtle flex items-center justify-between rounded-xl px-4 py-2.5">
                <span class="text-[11px] font-semibold text-[#47625A] dark:text-[#98B4AC]">Kursi &amp; Ruangan Tersedia</span>
                <span class="inline-flex items-center gap-1.5 font-mono text-sm font-extrabold text-emerald-700 dark:text-emerald-300">
                    <span class="ak-pulse-dot h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ $totalAvailableAll }}/{{ $totalUnitsAll }} Unit
                </span>
            </div>

            <div class="surface-subtle flex items-center justify-between rounded-xl px-4 py-2.5">
                <span class="text-[11px] font-semibold text-[#47625A] dark:text-[#98B4AC]">Status Kuota Akun</span>
                @if ($activeBooking)
                    <span class="rounded-md bg-amber-500/20 px-2 py-0.5 font-mono text-[11px] font-bold text-amber-800 dark:text-amber-300">
                        1 Sesi Aktif
                    </span>
                @else
                    <span class="rounded-md bg-emerald-500/15 px-2 py-0.5 font-mono text-[11px] font-bold text-emerald-800 dark:text-emerald-300">
                        Siap Reservasi
                    </span>
                @endif
            </div>
        </div>
    </section>

    @if ($activeBooking)
        <div class="ak-fade-up ak-delay-1 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-amber-500/30 bg-amber-500/12 p-4 dark:bg-amber-950/35">
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

    <div class="ak-stagger-grid grid gap-5 md:grid-cols-2">
        @forelse ($institutions as $inst)
            @php
                $units = $inst->facilityUnits;
                $total = $units->count();
                $occupied = $units->filter(fn ($u) => $u->activeBooking !== null)->count();
                $available = max(0, $total - $occupied);
                $pct = $total > 0 ? round(($available / $total) * 100) : 0;
                $floorsCount = $units->pluck('floor')->unique()->count();
                $delayClass = 'ak-delay-' . min(4, $loop->index + 1);

                $seatBreakdown = [
                    ['code' => 'L', 'label' => 'Meja Leseh (L)', 'count' => $units->where('seat_type', 'L')->filter(fn ($u) => $u->activeBooking === null)->count()],
                    ['code' => 'S', 'label' => 'Meja Standar (S)', 'count' => $units->where('seat_type', 'S')->filter(fn ($u) => $u->activeBooking === null)->count()],
                    ['code' => 'H', 'label' => 'Meja Tinggi (H)', 'count' => $units->where('seat_type', 'H')->filter(fn ($u) => $u->activeBooking === null)->count()],
                    ['code' => 'ROOM', 'label' => 'Ruangan', 'count' => $units->whereIn('seat_type', ['ROOM', 'F'])->filter(fn ($u) => $u->activeBooking === null)->count()],
                ];
            @endphp
            <a href="{{ route('visitor.map', $inst->slug) }}" wire:navigate
                class="surface-card card-lift ak-fade-up {{ $delayClass }} group flex flex-col justify-between rounded-2xl border-t-4 border-t-[#90DEC3] p-6 dark:border-t-[#54C2A0]">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-bold tracking-tight transition-colors group-hover:text-[#0F6E56] dark:group-hover:text-[#90DEC3]">
                                {{ $inst->name }}
                            </h2>
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

                    @if ($total > 0)
                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @foreach ($seatBreakdown as $sb)
                                @if ($units->whereIn('seat_type', $sb['code'] === 'ROOM' ? ['ROOM', 'F'] : [$sb['code']])->isNotEmpty())
                                    <span class="surface-subtle inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-[10px] font-semibold text-[#47625A] dark:text-[#98B4AC]">
                                        <span>{{ $sb['label'] }}:</span>
                                        <span class="font-mono font-bold text-[#0F6E56] dark:text-[#90DEC3]">{{ $sb['count'] }}</span>
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-5 space-y-3 border-t border-[#CBE8DC] pt-4 text-xs dark:border-[#213531]">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-medium text-[#47625A] dark:text-[#98B4AC]">Ketersediaan Unit</span>
                            <span class="rounded bg-[#90DEC3]/40 px-1.5 py-0.5 font-mono text-[10px] font-bold text-[#0F6E56] dark:bg-[#90DEC3]/20 dark:text-[#90DEC3]">
                                {{ $pct }}% Tersedia
                            </span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-[#E2F3EC] dark:bg-[#192825]">
                            <div class="ak-progress-bar h-full rounded-full bg-[#0F6E56] dark:bg-[#90DEC3]" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
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
                        <span class="inline-flex items-center gap-1 font-semibold text-[#0F6E56] dark:text-[#90DEC3]">
                            <span>Buka Denah Spasial 2D</span>
                            <span class="inline-block transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <div class="surface-card ak-fade-up col-span-full rounded-2xl p-8 text-center text-xs text-[#47625A] dark:text-[#98B4AC]">
                Tidak ada instansi yang cocok dengan pencarian Anda.
            </div>
        @endforelse
    </div>
</div>
