<div class="mx-auto max-w-4xl py-6">
    <div class="mb-4 flex items-center justify-between text-xs">
        <a href="{{ route('login') }}" wire:navigate class="font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
            &larr; Pilih Portal Lain
        </a>
        <a href="{{ route('login.visitor') }}" wire:navigate class="font-semibold text-[#4E5C52] hover:underline dark:text-[#B5C4B6]">
            Masuk sebagai Visitor &rarr;
        </a>
    </div>

    <div class="paper-card grid overflow-hidden rounded-3xl md:grid-cols-12">
        <!-- Decorative Institutional Side Panel -->
        <div class="hidden flex-col justify-between border-r border-[#D9D1BA] bg-[#EFEAD8] p-7 md:col-span-5 md:flex dark:border-[#27382F] dark:bg-[#1C2922]">
            <div>
                <span class="rounded-md border border-[#D9D1BA] bg-[#FAF7EC] px-2.5 py-1 font-mono text-[10px] font-bold uppercase dark:border-[#27382F] dark:bg-[#151F1A] dark:text-[#CAFFA1]">
                    Portal Pengelola Gedung
                </span>
                <h2 class="mt-4 font-serif text-2xl font-bold leading-snug">
                    Kendali Penuh Denah &amp; Kapasitas Gedung Anda.
                </h2>
                <p class="mt-2 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                    Gunakan Nama Instansi dan Kode Daftar resmi yang telah diterbitkan untuk mengelola ruang kelompok, meja indoor/outdoor, serta monitoring status live.
                </p>
            </div>

            <div class="mt-6 rounded-2xl border border-[#D9D1BA] bg-[#FAF7EC] p-4 text-xs dark:border-[#27382F] dark:bg-[#151F1A]">
                <p class="font-bold text-[#14452F] dark:text-[#CAFFA1]">Belum Punya Kode Daftar?</p>
                <p class="mt-1 text-[#4E5C52] dark:text-[#B5C4B6]">
                    Ajukan pendaftaran instansi baru melalui WhatsApp layanan verifikasi resmi kami.
                </p>
                <a href="https://wa.me//081222822531" target="_blank" rel="noopener noreferrer"
                    class="mt-3 inline-flex items-center gap-1.5 font-mono text-[11px] font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
                    wa.me//081222822531 &rarr;
                </a>
            </div>
        </div>

        <!-- Login Form Side -->
        <div class="p-6 sm:p-8 md:col-span-7">
            <h1 class="font-serif text-2xl font-bold tracking-tight">Sign In as Administrator</h1>
            <p class="mt-1 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                Masukkan Nama Instansi, Kode Daftar gedung, dan Password akun Anda.
            </p>

            <form wire:submit="login" class="mt-6 space-y-4">
                <div>
                    <label for="institution_name" class="block text-xs font-bold">Nama Instansi</label>
                    <input id="institution_name" type="text" wire:model="institution_name" required autofocus
                        placeholder="Contoh: Perpustakaan Pusat ITB"
                        class="mt-1.5 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922] dark:focus:border-[#CAFFA1]">
                    @error('institution_name')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="registration_code" class="block text-xs font-bold">Kode Daftar</label>
                    <input id="registration_code" type="text" wire:model="registration_code" required
                        placeholder="Contoh: LBR1ITB26"
                        class="mt-1.5 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 font-mono text-sm uppercase outline-none transition focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922] dark:focus:border-[#CAFFA1]">
                    @error('registration_code')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold">Password</label>
                    <input id="password" type="password" wire:model="password" required
                        placeholder="Masukkan password instansi"
                        class="mt-1.5 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922] dark:focus:border-[#CAFFA1]">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-[#14452F] px-4 py-2.5 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                    Masuk
                </button>
            </form>

            <div class="mt-5 border-t border-[#D9D1BA] pt-4 dark:border-[#27382F]">
                <p class="text-center text-[11px] text-[#4E5C52] dark:text-[#B5C4B6]">Ingin mendaftarkan gedung atau perpustakaan baru?</p>
                <div class="mt-2.5 grid gap-2 sm:grid-cols-2">
                    <button type="button" wire:click="$set('showRequestModal', true)"
                        class="rounded-xl border border-[#14452F]/30 bg-[#CAFFA1]/70 px-3 py-2.5 text-xs font-bold text-[#1A241E] transition hover:bg-[#CAFFA1] dark:border-[#CAFFA1]/30 dark:bg-[#1C2922] dark:text-[#CAFFA1]">
                        Isi Form Pengajuan WA
                    </button>
                    <a href="https://wa.me//081222822531" target="_blank" rel="noopener noreferrer"
                        class="rounded-xl bg-[#25D366] px-3 py-2.5 text-center text-xs font-bold text-[#1A241E] transition hover:opacity-90">
                        Daftar via WhatsApp &rarr;
                    </a>
                </div>
            </div>

            <details class="mt-5 rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/70 p-3.5 text-xs dark:border-[#27382F] dark:bg-[#1C2922]">
                <summary class="cursor-pointer font-bold">Kredensial Uji Instansi Mitra (Password: password)</summary>
                <div class="mt-2.5 grid gap-2">
                    <button type="button" wire:click="fillDemo('Perpustakaan Pusat ITB', 'LBR1ITB26', 'password')"
                        class="flex items-center justify-between rounded-lg border border-[#D9D1BA] bg-[#FAF7EC] px-3 py-2 text-left hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#151F1A]">
                        <span class="font-bold">Admin Perpus ITB</span>
                        <span class="font-mono text-[11px] text-[#14452F] dark:text-[#CAFFA1]">LBR1ITB26</span>
                    </button>
                    <button type="button" wire:click="fillDemo('Perpustakaan Universitas Indonesia', 'LBR1UI26', 'password')"
                        class="flex items-center justify-between rounded-lg border border-[#D9D1BA] bg-[#FAF7EC] px-3 py-2 text-left hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#151F1A]">
                        <span class="font-bold">Admin Perpus UI</span>
                        <span class="font-mono text-[11px] text-[#14452F] dark:text-[#CAFFA1]">LBR1UI26</span>
                    </button>
                    <button type="button" wire:click="fillDemo('PUT Politeknik Negeri Jakarta', 'PUT1PNJ26', 'password')"
                        class="flex items-center justify-between rounded-lg border border-[#D9D1BA] bg-[#FAF7EC] px-3 py-2 text-left hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#151F1A]">
                        <span class="font-bold">Admin PUT PNJ</span>
                        <span class="font-mono text-[11px] text-[#14452F] dark:text-[#CAFFA1]">PUT1PNJ26</span>
                    </button>
                </div>
            </details>
        </div>
    </div>

    @if ($showRequestModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 p-4 backdrop-blur-xs">
            <div class="paper-card w-full max-w-md rounded-2xl p-6">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <span class="rounded bg-[#25D366]/25 px-2 py-0.5 font-mono text-[10px] font-bold text-[#14452F] dark:text-[#CAFFA1]">
                            PENDAFTARAN ADMINISTRATOR VIA WHATSAPP
                        </span>
                        <h2 class="mt-1.5 font-serif text-xl font-bold">
                            Ajukan Pembuatan Kode Daftar
                        </h2>
                    </div>
                    <button type="button" wire:click="$set('showRequestModal', false)" class="text-lg font-bold">&times;</button>
                </div>
                <p class="mt-1 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                    Lengkapi data berikut untuk dikirim otomatis ke layanan WhatsApp penerbitan Kode Daftar resmi gedung Anda.
                </p>

                <div class="mt-4 space-y-3">
                    <div>
                        <label class="block text-xs font-bold">Nama Pendaftar</label>
                        <input type="text" wire:model.live="req_name" placeholder="Contoh: Dr. Rina Kartika"
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold">Nama Instansi Terkait</label>
                        <input type="text" wire:model.live="req_institution" placeholder="Contoh: Perpustakaan Pusat ITB"
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold">Password yang Diinginkan</label>
                        <input type="text" wire:model.live="req_desired_password" placeholder="Kata sandi akun admin"
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 font-mono text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold">Tujuan Permintaan Kebutuhan</label>
                        <textarea wire:model.live="req_purpose" rows="2" placeholder="Contoh: Pemetaan lantai 2 gedung perpustakaan..."
                            class="mt-1 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3 py-2 text-sm outline-none focus:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922]"></textarea>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-2.5">
                    <a href="{{ $this->whatsappUrl }}" target="_blank" rel="noopener noreferrer"
                        class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-2.5 text-xs font-bold text-[#1A241E] transition hover:opacity-90">
                        Kirim ke WhatsApp &rarr;
                    </a>
                    <button type="button" wire:click="$set('showRequestModal', false)"
                        class="rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] px-4 py-2.5 text-xs font-bold dark:border-[#27382F] dark:bg-[#1C2922]">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
