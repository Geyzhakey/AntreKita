<div class="space-y-8">
    <!-- Editorial Hero + Live Spatial Preview -->
    <section class="paper-card relative overflow-hidden rounded-3xl p-6 sm:p-10">
        <!-- Subtle Library Bookmark Ribbon -->
        <div class="pointer-events-none absolute top-0 right-10 hidden h-14 w-9 flex-col items-center justify-end bg-[#CAFFA1] pb-2 text-[#14452F] shadow-xs sm:flex"
            style="clip-path: polygon(0 0, 100% 0, 100% 100%, 50% 78%, 0 100%);">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 3v18M3 12h18" stroke-linecap="round" />
            </svg>
        </div>

        <div class="grid gap-8 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-7">
                <span class="inline-flex items-center gap-2 rounded-full border border-[#14452F]/20 bg-[#CAFFA1] px-3.5 py-1 font-mono text-[11px] font-bold tracking-wide text-[#1A241E]">
                    <span class="h-2 w-2 rounded-full bg-[#14452F]"></span>
                    EKOSISTEM PERPUSTAKAAN &amp; RUANG BACA CERDAS
                </span>

                <h1 class="mt-4 font-serif text-3xl leading-[1.15] font-bold tracking-tight sm:text-5xl">
                    Temukan Kursi &amp; Ruang Diskusi Sebelum Anda Melangkah.
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-[#4E5C52] sm:text-base dark:text-[#B5C4B6]">
                    AntreKita memetakan setiap <span class="font-bold text-[#1A241E] dark:text-[#CAFFA1]">Ruang Kelompok, Meja Indoor, Meja Outdoor,</span> hingga <span class="font-bold text-[#1A241E] dark:text-[#CAFFA1]">Kursi Baca</span> dalam denah spasial 2D secara real-time. Bebas drama kursi penuh, dilengkapi aturan adil 1 Akun = 1 Booking, dan pengingat WhatsApp H-5 menit otomatis.
                </p>

                <div class="mt-7 flex flex-wrap items-center gap-3">
                    @auth
                        @if (auth()->user()->isDeveloper())
                            <a href="{{ route('developer.console') }}" wire:navigate
                                class="rounded-xl bg-[#14452F] px-6 py-3 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                                Buka Pusat Kendali
                            </a>
                        @elseif (auth()->user()->isAdmin())
                            <a href="{{ route('admin.monitor') }}" wire:navigate
                                class="rounded-xl bg-[#14452F] px-6 py-3 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                                Buka Live Monitor Gedung
                            </a>
                        @else
                            <a href="{{ route('visitor.institutions') }}" wire:navigate
                                class="rounded-xl bg-[#14452F] px-6 py-3 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                                Jelajahi Denah Fasilitas
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login.visitor') }}" wire:navigate
                            class="rounded-xl bg-[#14452F] px-6 py-3 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                            Masuk sebagai Visitor
                        </a>
                        <a href="{{ route('register.visitor') }}" wire:navigate
                            class="rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] px-5 py-3 text-sm font-bold transition hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                            Daftar Akun Baru
                        </a>
                        <a href="{{ route('login.admin') }}" wire:navigate
                            class="rounded-xl px-4 py-3 text-xs font-bold text-[#4E5C52] underline-offset-4 hover:underline dark:text-[#B5C4B6]">
                            Portal Pengelola Gedung &rarr;
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Architectural Reading Room Mini-Map Widget -->
            <div class="lg:col-span-5">
                <div class="rounded-2xl border border-[#D9D1BA] bg-[#EFEAD8]/80 p-4 dark:border-[#27382F] dark:bg-[#1C2922]">
                    <div class="flex items-center justify-between border-b border-[#D9D1BA] pb-2.5 dark:border-[#27382F]">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-[#14452F] dark:text-[#CAFFA1]">
                            Ketersediaan Langsung Hari Ini
                        </span>
                        <span class="inline-flex items-center gap-1.5 font-mono text-[10px] font-bold text-[#14452F] dark:text-[#CAFFA1]">
                            <span class="h-2 w-2 animate-pulse rounded-full bg-[#10B981]"></span>
                            LIVE SYNC
                        </span>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-2.5">
                        <div class="rounded-xl border border-[#D9D1BA] bg-[#FAF7EC] p-3 text-center dark:border-[#27382F] dark:bg-[#151F1A]">
                            <div class="font-mono text-xl font-bold">{{ $institutions->count() }}</div>
                            <div class="text-[10px] font-semibold text-[#4E5C52] dark:text-[#9CB09E]">Lokasi Aktif</div>
                        </div>
                        <div class="rounded-xl border border-[#14452F]/25 bg-[#CAFFA1]/75 p-3 text-center text-[#1A241E]">
                            <div class="font-mono text-xl font-bold">{{ $availableUnits }}</div>
                            <div class="text-[10px] font-bold">Available</div>
                        </div>
                        <div class="rounded-xl border border-rose-300 bg-rose-100/80 p-3 text-center text-rose-950 dark:border-rose-800 dark:bg-rose-950/50 dark:text-rose-200">
                            <div class="font-mono text-xl font-bold">{{ $occupiedUnits }}</div>
                            <div class="text-[10px] font-bold">In Used</div>
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl border border-[#D9D1BA] bg-[#FAF7EC] p-3 dark:border-[#27382F] dark:bg-[#151F1A]">
                        <div class="flex items-center justify-between text-[11px] font-bold">
                            <span>Simulasi Status Denah 2D</span>
                            <span class="font-mono text-[10px] text-[#4E5C52] dark:text-[#9CB09E]">Lantai Utama</span>
                        </div>
                        <div class="mt-2.5 grid grid-cols-4 gap-2 font-mono text-[10px] font-bold">
                            <div class="rounded-lg border border-emerald-300 bg-[#CAFFA1]/60 p-2 text-center text-[#1A241E]">Ruang01</div>
                            <div class="rounded-lg border border-rose-300 bg-rose-100/90 p-2 text-center text-rose-900 dark:bg-rose-950/60 dark:text-rose-200">Ruang02</div>
                            <div class="rounded-lg border border-emerald-300 bg-[#CAFFA1]/60 p-2 text-center text-[#1A241E]">Meja04</div>
                            <div class="rounded-lg border border-amber-300 bg-amber-100/90 p-2 text-center text-amber-950 dark:bg-amber-950/60 dark:text-amber-200">Meja09</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3-Step Public Architecture (Zero Developer exposure) -->
    <section class="grid gap-4 md:grid-cols-3">
        <div class="paper-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="rounded-lg bg-[#CAFFA1] px-2.5 py-1 font-mono text-[10px] font-bold text-[#1A241E]">01 &middot; DENAH SPASIAL</span>
                <svg class="h-5 w-5 text-[#14452F] dark:text-[#CAFFA1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <rect x="3" y="3" width="18" height="18" rx="2" />
                    <path d="M9 3v18M3 9h18" />
                </svg>
            </div>
            <h2 class="mt-4 font-serif text-xl font-bold">Pilih Posisi Riil di Peta 2D</h2>
            <p class="mt-1.5 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                Pengelola memetakan posisi nyata setiap ruang diskusi, meja indoor, meja outdoor, dan kursi baca agar pengunjung tidak salah tempat.
            </p>
        </div>

        <div class="paper-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="rounded-lg bg-[#CAFFA1] px-2.5 py-1 font-mono text-[10px] font-bold text-[#1A241E]">02 &middot; PRESISI MENIT</span>
                <svg class="h-5 w-5 text-[#14452F] dark:text-[#CAFFA1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 7v5l3 2" stroke-linecap="round" />
                </svg>
            </div>
            <h2 class="mt-4 font-serif text-xl font-bold">Anti-Monopoli &amp; Waiting List</h2>
            <p class="mt-1.5 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                Aturan ketat 1 Akun = 1 Booking menjamin keadilan ruang publik. Jika unit terpakai, antrean berikutnya dapat melihat countdown sisa waktu secara transparan.
            </p>
        </div>

        <div class="paper-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <span class="rounded-lg bg-[#CAFFA1] px-2.5 py-1 font-mono text-[10px] font-bold text-[#1A241E]">03 &middot; HANDOVER H-5</span>
                <svg class="h-5 w-5 text-[#14452F] dark:text-[#CAFFA1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                </svg>
            </div>
            <h2 class="mt-4 font-serif text-xl font-bold">Notifikasi WA &amp; Slide to Finish</h2>
            <p class="mt-1.5 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                Lima menit sebelum waktu habis, pengingat otomatis terkirim ke pengguna aktif dan pengantre berikutnya sebelum serah-terima via Slide to Finish.
            </p>
        </div>
    </section>

    <!-- Partner Facilities Showcase -->
    <section class="paper-card rounded-3xl p-6">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[#D9D1BA] pb-4 dark:border-[#27382F]">
            <div>
                <h2 class="font-serif text-xl font-bold">Direktori Perpustakaan &amp; Ruang Belajar</h2>
                <p class="text-xs text-[#4E5C52] dark:text-[#B5C4B6]">Pilih fasilitas mitra yang sudah terintegrasi dengan sistem AntreKita.</p>
            </div>
            <a href="{{ route('login.visitor') }}" wire:navigate class="text-xs font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
                Masuk untuk reservasi &rarr;
            </a>
        </div>

        <div class="mt-5 grid gap-4 md:grid-cols-3">
            @foreach ($institutions as $inst)
                <div class="rounded-2xl border border-[#D9D1BA] bg-[#EFEAD8]/65 p-4 transition hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="font-serif text-base font-bold">{{ $inst->name }}</h3>
                        <span class="shrink-0 rounded-md bg-[#CAFFA1] px-2 py-0.5 font-mono text-[10px] font-bold text-[#1A241E]">
                            {{ $inst->facility_units_count }} unit
                        </span>
                    </div>
                    <p class="mt-1 text-xs font-semibold text-[#4E5C52] dark:text-[#9CB09E]">{{ $inst->building_type }} &middot; {{ $inst->city }}</p>
                    <p class="mt-2 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6] line-clamp-2">{{ $inst->description }}</p>
                </div>
            @endforeach
        </div>
    </section>
</div>
