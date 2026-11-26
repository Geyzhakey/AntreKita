<div class="mx-auto max-w-4xl py-8">
    <div class="mb-4 flex items-center justify-between text-xs">
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
            &larr; Pilih Portal Lain
        </a>
        <a href="{{ route('login.admin') }}" wire:navigate class="font-medium text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC]">
            Masuk sebagai Administrator &rarr;
        </a>
    </div>

    <div class="surface-card grid overflow-hidden rounded-[24px] md:grid-cols-12">
        <div class="surface-subtle hidden flex-col justify-between border-y-0 border-l-0 p-8 md:col-span-5 md:flex">
            <div>
                <span class="rounded-md bg-[#90DEC3] px-2.5 py-1 font-mono text-[10px] font-bold uppercase text-[#0E241E]">
                    Visitor Pass
                </span>
                <h2 class="mt-5 text-2xl font-bold leading-snug tracking-tight">
                    Akses Langsung Tanpa Birokrasi.
                </h2>
                <p class="mt-2.5 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                    Pantau peta kursi secara real-time, pesan slot menit presisi, dan terima notifikasi WhatsApp otomatis saat giliran tiba.
                </p>
            </div>

            <div class="surface-card rounded-2xl p-4 text-xs">
                <p class="font-bold text-[#0F6E56] dark:text-[#90DEC3]">Terbuka Untuk Semua</p>
                <p class="mt-1 text-[#47625A] dark:text-[#98B4AC]">
                    Langsung aktif dan siap melakukan reservasi di fasilitas mitra mana pun.
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 md:col-span-7">
            <h1 class="text-2xl font-bold tracking-tight">Sign In as Visitor</h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Masuk menggunakan Nama Pengguna, Email aktif, atau Nomor Telepon Anda.
            </p>

            <form wire:submit="login" class="mt-6 space-y-4">
                <div>
                    <label for="identifier" class="block text-xs font-semibold">Nama Pengguna / Email / Nomor Telepon</label>
                    <input id="identifier" type="text" wire:model="identifier" required autofocus
                        placeholder="Masukkan nama pengguna, email, atau nomor HP"
                        class="input-field mt-1.5 w-full rounded-xl px-3.5 py-2.5 text-sm">
                    @error('identifier')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold">Password</label>
                    <input id="password" type="password" wire:model="password" required
                        placeholder="Masukkan password akun Anda"
                        class="input-field mt-1.5 w-full rounded-xl px-3.5 py-2.5 text-sm">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-xs text-[#47625A] dark:text-[#98B4AC]">
                    <input type="checkbox" wire:model="remember" class="rounded border-[#CBE8DC] text-[#0F6E56]">
                    Ingat sesi saya
                </label>

                <button type="submit"
                    class="btn-brand w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs">
                    Masuk Sekarang
                </button>
            </form>

            <p class="mt-6 border-t border-[#CBE8DC] pt-4 text-center text-xs text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
                Belum punya akun Visitor?
                <a href="{{ route('register.visitor') }}" wire:navigate class="font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
                    Daftar Akun Baru &rarr;
                </a>
            </p>
        </div>
    </div>
</div>
