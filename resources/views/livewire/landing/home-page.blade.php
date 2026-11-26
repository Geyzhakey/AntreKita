@php
    $availabilityPct = $totalUnits > 0 ? round(($availableUnits / $totalUnits) * 100) : 100;
@endphp

<div class="space-y-14 pb-8">
    <!-- Clean Minimalist Hero -->
    <section class="grid gap-10 pt-2 lg:grid-cols-12 lg:items-center">
        <div class="lg:col-span-7">
            <span class="inline-flex items-center gap-2 rounded-full border border-[#0F6E56]/20 bg-[#90DEC3]/45 px-3.5 py-1 text-[11px] font-semibold text-[#0E241E] dark:border-[#90DEC3]/25 dark:bg-[#192825] dark:text-[#90DEC3]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#0F6E56] dark:bg-[#90DEC3]"></span>
                Terbuka untuk Kampus &amp; Publik &middot; Tanpa Aplikasi
            </span>

            <h1 class="mt-5 text-4xl leading-[1.1] font-extrabold tracking-[-0.03em] sm:text-5xl">
                Reservasi ruang belajar yang tertib dan terbuka.
            </h1>

            <p class="mt-4 max-w-xl text-sm leading-relaxed text-[#47625A] sm:text-base dark:text-[#98B4AC]">
                Cek ketersediaan meja, kursi baca, dan ruang diskusi langsung dari peta 2D secara real-time di berbagai gedung perpustakaan mitra.
            </p>

            <div class="mt-7 flex flex-wrap items-center gap-3">
                @auth
                    @if (auth()->user()->isDeveloper())
                        <a href="{{ route('developer.console') }}" wire:navigate
                            class="btn-brand rounded-lg px-6 py-3 text-sm font-semibold shadow-xs">
                            Buka Pusat Kendali &rarr;
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('admin.monitor') }}" wire:navigate
                            class="btn-brand rounded-lg px-6 py-3 text-sm font-semibold shadow-xs">
                            Buka Live Monitor &rarr;
                        </a>
                    @else
                        <a href="{{ route('visitor.institutions') }}" wire:navigate
                            class="btn-brand rounded-lg px-6 py-3 text-sm font-semibold shadow-xs">
                            Jelajahi Fasilitas &rarr;
                        </a>
                    @endif
                @else
                    <a href="{{ route('login.visitor') }}" wire:navigate
                        class="btn-brand rounded-lg px-6 py-3 text-sm font-semibold shadow-xs">
                        Mulai Reservasi &rarr;
                    </a>
                    <a href="{{ route('login.admin') }}" wire:navigate
                        class="surface-card rounded-lg px-5 py-3 text-sm font-semibold text-[#0E241E] transition hover:border-[#0F6E56] dark:text-[#E6F2EE]">
                        Portal Pengelola
                    </a>
                @endauth
            </div>
        </div>

        <!-- Clean Multi-Facility Live Overview Card (Neutral Across All Partners) -->
        <div class="lg:col-span-5">
            <div class="surface-card rounded-2xl border-t-4 border-t-[#90DEC3] p-6">
                <div class="flex items-center justify-between border-b border-[#CBE8DC] pb-3.5 dark:border-[#213531]">
                    <span class="text-xs font-bold tracking-tight">Ringkasan Ketersediaan Jaringan</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#90DEC3]/45 px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E] dark:bg-[#192825] dark:text-[#90DEC3]">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                        LIVE
                    </span>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2.5 text-center">
                    <div class="surface-subtle rounded-xl p-3.5">
                        <div class="font-mono text-2xl font-extrabold tabular-nums">{{ $institutions->count() }}</div>
                        <div class="mt-0.5 text-[11px] font-medium text-[#47625A] dark:text-[#98B4AC]">Lokasi Aktif</div>
                    </div>
                    <div class="rounded-xl bg-[#90DEC3]/55 p-3.5 text-[#0E241E] dark:bg-[#192D28] dark:text-[#90DEC3]">
                        <div class="font-mono text-2xl font-extrabold tabular-nums">{{ $availableUnits }}</div>
                        <div class="mt-0.5 text-[11px] font-bold">Available</div>
                    </div>
                    <div class="rounded-xl border border-rose-500/25 bg-rose-500/12 p-3.5 text-rose-800 dark:text-rose-200">
                        <div class="font-mono text-2xl font-extrabold tabular-nums">{{ $occupiedUnits }}</div>
                        <div class="mt-0.5 text-[11px] font-bold">In Used</div>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between rounded-xl border border-[#CBE8DC] bg-[#E2F3EC]/60 px-4 py-3 text-xs dark:border-[#213531] dark:bg-[#192825]">
                    <span class="font-medium text-[#47625A] dark:text-[#98B4AC]">Rasio kursi siap pakai</span>
                    <span class="font-mono text-sm font-extrabold text-[#0F6E56] dark:text-[#90DEC3]">{{ $availabilityPct }}% Tersedia</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 3 Compact Value Highlights -->
    <section id="cara-kerja" class="grid gap-4 md:grid-cols-3">
        <div class="surface-card rounded-2xl border-t-4 border-t-[#90DEC3] p-5">
            <h2 class="text-sm font-bold tracking-tight">1. Denah Spasial 2D</h2>
            <p class="mt-1.5 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                Lihat tata letak meja dan ruangan sebenarnya sebelum memilih tempat.
            </p>
        </div>

        <div class="surface-card rounded-2xl border-t-4 border-t-[#0F6E56] p-5 dark:border-t-[#54C2A0]">
            <h2 class="text-sm font-bold tracking-tight">2. Adil &amp; Anti-Monopoli</h2>
            <p class="mt-1.5 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                1 akun hanya memegang 1 kursi aktif dengan antrean Waiting List transparan.
            </p>
        </div>

        <div class="surface-card rounded-2xl border-t-4 border-t-amber-400 p-5">
            <h2 class="text-sm font-bold tracking-tight">3. Pengingat WhatsApp H-5</h2>
            <p class="mt-1.5 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                Notifikasi otomatis 5 menit sebelum giliran usai dan serah-terima Slide to Finish.
            </p>
        </div>
    </section>

    <!-- Equal Directory of All Active Partner Facilities -->
    <section id="fasilitas" class="space-y-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold tracking-tight">Fasilitas Terdaftar</h2>
                <p class="mt-0.5 text-xs text-[#47625A] dark:text-[#98B4AC]">
                    Pilih gedung mitra untuk mengecek ketersediaan tempat secara langsung.
                </p>
            </div>
            <a href="{{ auth()->check() ? route('visitor.institutions') : route('login.visitor') }}" wire:navigate
                class="text-xs font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
                Lihat semua lokasi &rarr;
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($institutions as $inst)
                <div class="surface-card flex flex-col justify-between rounded-2xl p-5 transition hover:border-[#0F6E56]/50">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-base font-bold tracking-tight">{{ $inst->name }}</h3>
                                <p class="mt-0.5 text-xs text-[#47625A] dark:text-[#98B4AC]">{{ $inst->building_type }} &middot; {{ $inst->city }}</p>
                            </div>
                            <span class="shrink-0 rounded-md bg-[#90DEC3]/75 px-2.5 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">
                                {{ $inst->facility_units_count }} unit
                            </span>
                        </div>
                        <p class="mt-2.5 line-clamp-2 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">{{ $inst->description }}</p>
                    </div>
                    <div class="mt-4 border-t border-[#CBE8DC] pt-3 font-mono text-[11px] text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                        {{ $inst->address }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
