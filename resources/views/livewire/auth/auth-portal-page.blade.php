<div class="mx-auto max-w-4xl py-10">
    <div class="text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-[#0F6E56]/20 bg-[#90DEC3]/55 px-3.5 py-1 font-mono text-[10px] font-bold uppercase tracking-widest text-[#0E241E] dark:border-[#90DEC3]/25 dark:bg-[#192825] dark:text-[#90DEC3]">
            Portal Akses AntreKita
        </span>
        <h1 class="mt-3 text-3xl font-extrabold tracking-[-0.025em] sm:text-4xl">
            Pilih Jalur Masuk Anda
        </h1>
        <p class="mt-1.5 text-xs text-[#47625A] dark:text-[#98B4AC]">
            Akses terpisah antara pengunjung ruang belajar dan administrator pengelola gedung.
        </p>
    </div>

    <div class="mt-8 grid gap-5 md:grid-cols-2">
        <div class="surface-card flex flex-col justify-between rounded-2xl border-t-4 border-t-[#90DEC3] p-7">
            <div>
                <div class="flex items-center justify-between">
                    <span class="rounded-md bg-[#90DEC3]/65 px-2.5 py-1 font-mono text-[10px] font-bold text-[#0E241E] dark:bg-[#90DEC3]/20 dark:text-[#90DEC3]">VISITOR / UMUM</span>
                    <span class="surface-subtle flex h-10 w-10 items-center justify-center rounded-xl text-[#0F6E56] dark:text-[#90DEC3]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                </div>
                <h2 class="mt-5 text-2xl font-bold tracking-tight">Sign In / Up as Visitor</h2>
                <p class="mt-2 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                    Terbuka bagi mahasiswa dari kampus mana pun dan pengunjung umum untuk melihat denah 2D dan memesan slot waktu.
                </p>
            </div>

            <div class="mt-7 grid grid-cols-2 gap-3">
                <a href="{{ route('login.visitor') }}" wire:navigate
                    class="btn-brand rounded-lg px-4 py-2.5 text-center text-xs font-semibold shadow-xs">
                    Sign In Visitor
                </a>
                <a href="{{ route('register.visitor') }}" wire:navigate
                    class="surface-subtle rounded-lg px-4 py-2.5 text-center text-xs font-semibold transition hover:border-[#0F6E56]/40 dark:hover:border-[#90DEC3]/40">
                    Daftar Baru
                </a>
            </div>
        </div>

        <div class="surface-card flex flex-col justify-between rounded-2xl border-t-4 border-t-[#0F6E56] p-7 dark:border-t-[#54C2A0]">
            <div>
                <div class="flex items-center justify-between">
                    <span class="surface-subtle rounded-md px-2.5 py-1 font-mono text-[10px] font-bold text-[#0F6E56] dark:text-[#90DEC3]">ADMINISTRATOR GEDUNG</span>
                    <span class="surface-subtle flex h-10 w-10 items-center justify-center rounded-xl text-[#0F6E56] dark:text-[#90DEC3]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <h2 class="mt-5 text-2xl font-bold tracking-tight">Sign In as Administrator</h2>
                <p class="mt-2 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                    Khusus pengelola fasilitas atau perpustakaan yang memiliki Nama Instansi dan Kode Daftar resmi gedung.
                </p>
            </div>

            <div class="mt-7">
                <a href="{{ route('login.admin') }}" wire:navigate
                    class="block w-full rounded-lg bg-[#0E241E] px-4 py-2.5 text-center text-xs font-semibold text-[#90DEC3] transition hover:opacity-90 dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                    Masuk sebagai Administrator &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
