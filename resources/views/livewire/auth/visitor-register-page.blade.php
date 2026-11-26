<div class="mx-auto max-w-4xl py-8">
    <div class="mb-4 flex items-center justify-between text-xs">
        <a href="{{ route('login.visitor') }}" wire:navigate class="font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
            &larr; Kembali ke Sign In Visitor
        </a>
    </div>

    <div class="surface-card grid overflow-hidden rounded-[24px] md:grid-cols-12">
        <div class="surface-subtle hidden flex-col justify-between border-y-0 border-l-0 p-8 md:col-span-5 md:flex">
            <div>
                <span class="rounded-md bg-[#90DEC3] px-2.5 py-1 font-mono text-[10px] font-bold uppercase text-[#0E241E]">
                    Registrasi Instan
                </span>
                <h2 class="mt-5 text-2xl font-bold leading-snug tracking-tight">
                    Satu Akun untuk Semua Ruang Belajar.
                </h2>
                <p class="mt-2.5 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                    Nomor WhatsApp Anda digunakan untuk mengirim pengingat otomatis 5 menit sebelum giliran berakhir atau dimulai.
                </p>
            </div>

            <div class="surface-card rounded-2xl p-4 text-xs">
                <p class="font-bold text-[#0F6E56] dark:text-[#90DEC3]">Aturan Kebersamaan</p>
                <p class="mt-1 text-[#47625A] dark:text-[#98B4AC]">
                    1 akun dan nomor telepon hanya memegang 1 booking aktif atau 1 antrean pada saat bersamaan.
                </p>
            </div>
        </div>

        <div class="p-6 sm:p-8 md:col-span-7">
            <h1 class="text-2xl font-bold tracking-tight">Sign Up Visitor Baru</h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Isi data berikut — akun langsung aktif tanpa menunggu verifikasi manual.
            </p>

            <form wire:submit="register" class="mt-5 space-y-4">
                <div class="grid gap-3.5 sm:grid-cols-2">
                    <div>
                        <label for="username" class="block text-xs font-semibold">Nama Pengguna</label>
                        <input id="username" type="text" wire:model="username" required placeholder="nama_pengguna"
                            class="input-field mt-1 w-full rounded-xl px-3.5 py-2.5 font-mono text-sm">
                        @error('username') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="affiliation" class="block text-xs font-semibold">Instansi</label>
                        <input id="affiliation" type="text" wire:model="affiliation" required placeholder="Kampus / Instansi / Umum"
                            class="input-field mt-1 w-full rounded-xl px-3.5 py-2.5 text-sm">
                        @error('affiliation') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold">Email Aktif</label>
                    <input id="email" type="email" wire:model="email" required placeholder="email@domain.com"
                        class="input-field mt-1 w-full rounded-xl px-3.5 py-2.5 text-sm">
                    @error('email') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="whatsapp_number" class="block text-xs font-semibold">Nomor Telepon (WhatsApp Aktif)</label>
                    <input id="whatsapp_number" type="text" wire:model="whatsapp_number" required placeholder="0812xxxxxxxx"
                        class="input-field mt-1 w-full rounded-xl px-3.5 py-2.5 font-mono text-sm">
                    @error('whatsapp_number') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-3.5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-xs font-semibold">Password</label>
                        <input id="password" type="password" wire:model="password" required
                            class="input-field mt-1 w-full rounded-xl px-3.5 py-2.5 text-sm">
                        @error('password') <p class="mt-1 text-[11px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold">Konfirmasi Password</label>
                        <input id="password_confirmation" type="password" wire:model="password_confirmation" required
                            class="input-field mt-1 w-full rounded-xl px-3.5 py-2.5 text-sm">
                    </div>
                </div>

                <button type="submit"
                    class="btn-brand w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs">
                    Daftar &amp; Masuk Sekarang
                </button>
            </form>
        </div>
    </div>
</div>
