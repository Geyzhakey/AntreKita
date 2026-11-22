<div class="flex min-h-screen items-center justify-center px-4 py-10">

    <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-xl">

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-indigo-600">
                AntreKita
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Sistem antrean yang lebih mudah dan teratur.
            </p>
        </div>

        <h2 class="mb-2 text-2xl font-bold text-slate-800">
            Selamat datang!
        </h2>

        <p class="mb-7 text-sm text-slate-500">
            Masuk ke akun AntreKita kamu.
        </p>

        <form wire:submit="login" class="space-y-5">

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">
                    Email
                </label>

                <input
                    type="email"
                    wire:model="email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@email.com"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                @error('email')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
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
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                @error('password')
                    <p class="mt-1 text-sm text-red-500">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" wire:model="remember" class="rounded border-slate-300">
                Ingat saya
            </label>

            <button
                type="submit"
                class="w-full rounded-lg bg-indigo-600 py-3 font-semibold text-white transition hover:bg-indigo-700">
                Masuk
            </button>

        </form>

        <p class="mt-7 text-center text-sm text-slate-600">
            Belum memiliki akun?

            <a href="{{ route('register') }}"
               class="font-semibold text-indigo-600 hover:underline"
               wire:navigate>
                Daftar sekarang
            </a>
        </p>

    </div>
</div>
