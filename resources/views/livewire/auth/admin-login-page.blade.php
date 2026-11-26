<div class="mx-auto max-w-4xl py-8">
    <div class="mb-4 flex items-center justify-between text-xs">
        <a href="{{ route('login') }}" wire:navigate class="font-semibold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
            &larr; Pilih Portal Lain
        </a>
        <a href="{{ route('login.visitor') }}" wire:navigate class="font-medium text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC]">
            Masuk sebagai Visitor &rarr;
        </a>
    </div>

    <div class="surface-card grid overflow-hidden rounded-[24px] md:grid-cols-12">
        <div class="surface-subtle hidden flex-col justify-between border-y-0 border-l-0 p-8 md:col-span-5 md:flex">
            <div>
                <span class="surface-card rounded-md px-2.5 py-1 font-mono text-[10px] font-bold uppercase text-[#0F6E56] dark:text-[#90DEC3]">
                    Portal Pengelola Gedung
                </span>
                <h2 class="mt-5 text-2xl font-bold leading-snug tracking-tight">
                    Kendali Penuh Denah &amp; Kapasitas Gedung.
                </h2>
                <p class="mt-2.5 text-xs leading-relaxed text-[#47625A] dark:text-[#98B4AC]">
                    Masuk menggunakan Nama Instansi, Kode Daftar gedung, dan Password akun resmi Anda.
                </p>
            </div>

            <div class="surface-card rounded-2xl p-4 text-xs">
                <p class="font-bold text-[#0F6E56] dark:text-[#90DEC3]">Pendaftaran Akun Instansi Baru</p>
                <p class="mt-1 text-[#47625A] dark:text-[#98B4AC]">
                    Klik nomor WhatsApp di bawah — format pendaftaran akan otomatis terisi di pesan WhatsApp Anda.
                </p>
                <a href="{{ $this->whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                    class="mt-3 inline-flex items-center gap-1.5 font-mono text-xs font-bold text-[#0F6E56] hover:underline dark:text-[#90DEC3]">
                    0812-2282-2531 &rarr;
                </a>
            </div>
        </div>

        <div class="p-6 sm:p-8 md:col-span-7">
            <h1 class="text-2xl font-bold tracking-tight">Sign In as Administrator</h1>
            <p class="mt-1 text-xs text-[#47625A] dark:text-[#98B4AC]">
                Masukkan Nama Instansi, Kode Daftar gedung, dan Password akun Anda.
            </p>

            <form wire:submit="login" class="mt-6 space-y-4">
                <div>
                    <label for="institution_name" class="block text-xs font-semibold">Nama Instansi</label>
                    <input id="institution_name" type="text" wire:model="institution_name" required autofocus
                        placeholder="Masukkan nama instansi / perpustakaan"
                        class="input-field mt-1.5 w-full rounded-xl px-3.5 py-2.5 text-sm">
                    @error('institution_name')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="registration_code" class="block text-xs font-semibold">Kode Daftar</label>
                    <input id="registration_code" type="text" wire:model="registration_code" required
                        placeholder="Masukkan kode daftar resmi gedung"
                        class="input-field mt-1.5 w-full rounded-xl px-3.5 py-2.5 font-mono text-sm uppercase">
                    @error('registration_code')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold">Password</label>
                    <input id="password" type="password" wire:model="password" required
                        placeholder="Masukkan password instansi"
                        class="input-field mt-1.5 w-full rounded-xl px-3.5 py-2.5 text-sm">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="btn-brand w-full rounded-lg px-4 py-2.5 text-sm font-semibold shadow-xs">
                    Masuk
                </button>
            </form>

            <div class="mt-6 border-t border-[#CBE8DC] pt-5 text-center dark:border-[#213531]">
                <p class="text-xs text-[#47625A] dark:text-[#98B4AC]">Belum memiliki akun pengelola gedung?</p>
                <a href="{{ $this->whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                    class="mt-2.5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#25D366] px-4 py-2.5 text-xs font-bold text-[#0E241E] shadow-xs transition hover:opacity-90">
                    Daftar Akun Administrator via WhatsApp (0812-2282-2531) &rarr;
                </a>
                <p class="mt-2 text-[11px] text-[#47625A] dark:text-[#98B4AC]">
                    Teks formulir pendaftaran akan otomatis disiapkan saat WhatsApp terbuka.
                </p>
            </div>
        </div>
    </div>
</div>
