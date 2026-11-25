<div class="mx-auto max-w-4xl py-6">
    <div class="mb-4 flex items-center justify-between text-xs">
        <a href="{{ route('login.visitor') }}" wire:navigate class="font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
            &larr; Kembali ke Sign In Visitor
        </a>
    </div>

    <div class="paper-card grid overflow-hidden rounded-3xl md:grid-cols-12">
        <div class="relative hidden flex-col justify-between border-r border-[#D9D1BA] bg-[#EFEAD8] p-7 md:col-span-5 md:flex dark:border-[#27382F] dark:bg-[#1C2922]">
            <div>
                <span class="rounded-md bg-[#CAFFA1] px-2.5 py-1 font-mono text-[10px] font-bold uppercase text-[#1A241E]">
                    Registrasi Anggota
                </span>
                <h2 class="mt-4 font-serif text-2xl font-bold leading-snug">
                    Satu Akun untuk Seluruh Perpustakaan Mitra.
                </h2>
                <p class="mt-2 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                    Nomor telepon Anda digunakan untuk mengirim pengingat WhatsApp otomatis 5 menit sebelum sesi belajar berakhir.
                </p>
            </div>

            <div class="rounded-2xl border border-[#D9D1BA] bg-[#FAF7EC] p-4 text-xs dark:border-[#27382F] dark:bg-[#151F1A]">
                <p class="font-bold text-[#14452F] dark:text-[#CAFFA1]">Aturan Kebersamaan</p>
                <p class="mt-1 text-[#4E5C52] dark:text-[#B5C4B6]">
                    1 akun dan nomor telepon hanya dapat memegang 1 booking aktif atau 1 antrean waiting list pada saat bersamaan.
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 md:col-span-7">
            <h1 class="font-serif text-2xl font-bold tracking-tight">Sign Up Visitor Baru</h1>
            <p class="mt-1 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                Isi data berikut untuk mendaftarkan akun pengunjung baru.
            </p>

            <form wire:submit="register" class="mt-5 space-y-4">
                <div class="grid gap-3.5 sm:grid-cols-2">
                    <div>
                        <label for="username" class="block text-xs font-bold">Nama Pengguna</label>
                        <input id="username" type="text" wire:model="username" required placeholder="dandy_aulia"
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 font-mono text-sm outline-none focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922]">
                        @error('username') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="affiliation" class="block text-xs font-bold">Instansi</label>
                        <input id="affiliation" type="text" wire:model="affiliation" required placeholder="ITB / UI / PNJ / Umum"
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922]">
                        @error('affiliation') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold">Email Aktif</label>
                    <input id="email" type="email" wire:model="email" required placeholder="pengguna@gmail.com"
                        class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922]">
                    @error('email') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="whatsapp_number" class="block text-xs font-bold">Nomor Telepon (WhatsApp Aktif)</label>
                    <input id="whatsapp_number" type="text" wire:model="whatsapp_number" required placeholder="081234567890"
                        class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 font-mono text-sm outline-none focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922]">
                    @error('whatsapp_number') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-3.5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-xs font-bold">Password</label>
                        <input id="password" type="password" wire:model="password" required
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922]">
                        @error('password') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" wire:model="password_confirmation" required
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922]">
                    </div>
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-[#14452F] px-4 py-2.5 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                    Daftar &amp; Masuk ke AntreKita
                </button>
            </form>
        </div>
    </div>
</div>
