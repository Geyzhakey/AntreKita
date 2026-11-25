<div class="mx-auto max-w-4xl py-6">
    <div class="mb-4 flex items-center justify-between text-xs">
        <a href="{{ route('login') }}" wire:navigate class="font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
            &larr; Pilih Portal Lain
        </a>
        <a href="{{ route('login.admin') }}" wire:navigate class="font-semibold text-[#4E5C52] hover:underline dark:text-[#B5C4B6]">
            Masuk sebagai Administrator &rarr;
        </a>
    </div>

    <div class="paper-card grid overflow-hidden rounded-3xl md:grid-cols-12">
        <!-- Decorative Botanical Library Bookplate Side Panel -->
        <div class="relative hidden flex-col justify-between border-r border-[#D9D1BA] bg-[#EFEAD8] p-7 md:col-span-5 md:flex dark:border-[#27382F] dark:bg-[#1C2922]">
            <div>
                <span class="rounded-md bg-[#CAFFA1] px-2.5 py-1 font-mono text-[10px] font-bold uppercase text-[#1A241E]">
                    Visitor Pass
                </span>
                <h2 class="mt-4 font-serif text-2xl font-bold leading-snug">
                    Ruang Belajar Teratur, Tanpa Antrean Kacau.
                </h2>
                <p class="mt-2 text-xs leading-relaxed text-[#4E5C52] dark:text-[#B5C4B6]">
                    Gunakan akun Visitor Anda untuk mengecek posisi meja dan ruang diskusi yang masih tersedia sebelum datang ke lokasi.
                </p>
            </div>

            <div class="mt-8 flex justify-center opacity-80">
                <svg class="h-36 w-36 text-[#14452F] dark:text-[#CAFFA1]" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.4">
                    <path d="M22 96V42a38 38 0 0 1 76 0v54" />
                    <path d="M14 96h92" stroke-linecap="round" />
                    <path d="M40 96V56a20 20 0 0 1 40 0v40" />
                    <circle cx="60" cy="24" r="4" />
                    <path d="M46 66h28M46 76h28" stroke-linecap="round" />
                </svg>
            </div>
        </div>

        <!-- Form Side -->
        <div class="p-6 sm:p-8 md:col-span-7">
            <h1 class="font-serif text-2xl font-bold tracking-tight">Sign In as Visitor</h1>
            <p class="mt-1 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                Masuk menggunakan Nama Pengguna, Email aktif, atau Nomor Telepon Anda.
            </p>

            <form wire:submit="login" class="mt-6 space-y-4">
                <div>
                    <label for="identifier" class="block text-xs font-bold">Nama Pengguna / Email / Nomor Telepon</label>
                    <input id="identifier" type="text" wire:model="identifier" required autofocus
                        placeholder="dandy_aulia / pengguna1@gmail.com / 0812..."
                        class="mt-1.5 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922] dark:focus:border-[#CAFFA1]">
                    @error('identifier')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold">Password</label>
                    <input id="password" type="password" wire:model="password" required
                        placeholder="Masukkan password akun Anda"
                        class="mt-1.5 w-full rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/60 px-3.5 py-2.5 text-sm outline-none transition focus:border-[#14452F] focus:bg-[#FAF7EC] dark:border-[#27382F] dark:bg-[#1C2922] dark:focus:border-[#CAFFA1]">
                    @error('password')
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                    <input type="checkbox" wire:model="remember" class="rounded border-[#D9D1BA] text-[#14452F]">
                    Ingat sesi saya
                </label>

                <button type="submit"
                    class="w-full rounded-xl bg-[#14452F] px-4 py-2.5 text-sm font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                    Masuk Sekarang
                </button>
            </form>

            <p class="mt-5 text-center text-xs text-[#4E5C52] dark:text-[#B5C4B6]">
                Belum punya akun Visitor?
                <a href="{{ route('register.visitor') }}" wire:navigate class="font-bold text-[#14452F] hover:underline dark:text-[#CAFFA1]">
                    Sign Up Visitor Baru
                </a>
            </p>

            <details class="mt-5 rounded-xl border border-[#D9D1BA] bg-[#EFEAD8]/70 p-3.5 text-xs dark:border-[#27382F] dark:bg-[#1C2922]">
                <summary class="cursor-pointer font-bold">Akun Uji Visitor (Password: password)</summary>
                <div class="mt-2.5 grid gap-2">
                    <button type="button" wire:click="fillDemo('dandy_aulia')"
                        class="flex items-center justify-between rounded-lg border border-[#D9D1BA] bg-[#FAF7EC] px-3 py-2 text-left hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#151F1A]">
                        <span class="font-bold">Visitor 1 (Aktif Meja09)</span>
                        <span class="font-mono text-[11px] text-[#14452F] dark:text-[#CAFFA1]">dandy_aulia</span>
                    </button>
                    <button type="button" wire:click="fillDemo('geyzha_k')"
                        class="flex items-center justify-between rounded-lg border border-[#D9D1BA] bg-[#FAF7EC] px-3 py-2 text-left hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#151F1A]">
                        <span class="font-bold">Visitor 2 (Waiting Meja09)</span>
                        <span class="font-mono text-[11px] text-[#14452F] dark:text-[#CAFFA1]">geyzha_k</span>
                    </button>
                    <button type="button" wire:click="fillDemo('alif_fhatur')"
                        class="flex items-center justify-between rounded-lg border border-[#D9D1BA] bg-[#FAF7EC] px-3 py-2 text-left hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#151F1A]">
                        <span class="font-bold">Visitor 3 (Siap Booking)</span>
                        <span class="font-mono text-[11px] text-[#14452F] dark:text-[#CAFFA1]">alif_fhatur</span>
                    </button>
                </div>
            </details>
        </div>
    </div>
</div>
