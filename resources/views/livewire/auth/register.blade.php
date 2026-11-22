<div class="flex min-h-screen items-center justify-center px-4 py-10">

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-indigo-600">
                AntreKita
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Mulai pengalaman antrean yang lebih mudah.
            </p>
        </div>

        <h2 class="mb-2 text-2xl font-bold text-slate-800">
            Buat akun
        </h2>

        <p class="mb-7 text-sm text-slate-500">
            Daftarkan diri untuk menggunakan AntreKita.
        </p>

        <form wire:submit="register" class="space-y-5">

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Nama lengkap
                </label>

                <input
                    type="text"
                    wire:model="name"
                    required
                    autocomplete="name"
                    placeholder="Nama lengkap"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Email
                </label>

                <input
                    type="email"
                    wire:model="email"
                    required
                    autocomplete="username"
                    placeholder="nama@email.com"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Password
                </label>

                <input
                    type="password"
                    wire:model="password"
                    required
                    autocomplete="new-password"
                    placeholder="Buat password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Konfirmasi password
                </label>

                <input
                    type="password"
                    wire:model="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-3 font-semibold text-white transition hover:bg-indigo-700">
                Daftar
            </button>

        </form>

        <p class="mt-7 text-center text-sm text-slate-600">
            Sudah punya akun?

            <a href="{{ route('login') }}"
               class="font-semibold text-indigo-600 hover:underline"
               wire:navigate>
                Masuk
            </a>
        </p>

    </div>
</div>
