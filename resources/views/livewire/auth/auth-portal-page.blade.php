<div class="mx-auto max-w-4xl py-8">
    <div class="text-center">
        <span class="inline-flex items-center gap-2 rounded-full border border-[#14452F]/20 bg-[#CAFFA1] px-3.5 py-1 font-mono text-[11px] font-bold uppercase tracking-wider text-[#1A241E]">
            Portal Akses AntreKita
        </span>
        <h1 class="mt-3 font-serif text-3xl font-bold tracking-tight sm:text-4xl">
            Pilih Jalur Masuk Anda
        </h1>
        <p class="mt-1.5 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
            AntreKita memisahkan akses pengunjung ruang baca dengan portal pengelola gedung / instansi.
        </p>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <div class="paper-card flex flex-col justify-between rounded-3xl p-7 transition hover:border-[#14452F] dark:hover:border-[#CAFFA1]">
            <div>
                <div class="flex items-center justify-between">
                    <span class="rounded-lg bg-[#CAFFA1] px-2.5 py-1 font-mono text-[11px] font-bold text-[#1A241E]">VISITOR / PENGGUNA</span>
                    <svg class="h-6 w-6 text-[#14452F] dark:text-[#CAFFA1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </div>
                <h2 class="mt-5 font-serif text-2xl font-bold">Sign In / Up as Visitor</h2>
                <p class="mt-2 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                    Untuk mahasiswa, pelajar, atau pengunjung umum yang ingin melihat peta ketersediaan meja/ruangan secara live dan memesan slot waktu.
                </p>
                <ul class="mt-4 space-y-1.5 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                    <li>&bull; Login via Nama Pengguna, Email, atau Nomor Telepon</li>
                    <li>&bull; Pendaftaran langsung aktif dengan WhatsApp</li>
                </ul>
            </div>

            <div class="mt-7 grid grid-cols-2 gap-3">
                <a href="{{ route('login.visitor') }}" wire:navigate
                    class="rounded-xl bg-[#14452F] px-4 py-2.5 text-center text-xs font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                    Sign In Visitor
                </a>
                <a href="{{ route('register.visitor') }}" wire:navigate
                    class="rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] px-4 py-2.5 text-center text-xs font-bold transition hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                    Daftar Baru
                </a>
            </div>
        </div>

        <div class="paper-card flex flex-col justify-between rounded-3xl p-7 transition hover:border-[#14452F] dark:hover:border-[#CAFFA1]">
            <div>
                <div class="flex items-center justify-between">
                    <span class="rounded-lg border border-[#D9D1BA] bg-[#EFEAD8] px-2.5 py-1 font-mono text-[11px] font-bold dark:border-[#27382F] dark:bg-[#1C2922] dark:text-[#CAFFA1]">ADMINISTRATOR INSTANSI</span>
                    <svg class="h-6 w-6 text-[#14452F] dark:text-[#CAFFA1]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h2 class="mt-5 font-serif text-2xl font-bold">Sign In as Administrator</h2>
                <p class="mt-2 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                    Khusus pengelola perpustakaan, gedung layanan kampus, atau pengawas fasilitas yang telah memiliki Kode Daftar resmi gedung.
                </p>
                <ul class="mt-4 space-y-1.5 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                    <li>&bull; Masuk menggunakan Nama Instansi + Kode Daftar + Password</li>
                    <li>&bull; Pendaftaran pengelola baru langsung terhubung ke WhatsApp layanan AntreKita</li>
                </ul>
            </div>

            <div class="mt-7">
                <a href="{{ route('login.admin') }}" wire:navigate
                    class="block w-full rounded-xl bg-[#1A241E] px-4 py-2.5 text-center text-xs font-bold text-[#CAFFA1] transition hover:opacity-90 dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                    Masuk sebagai Administrator &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
