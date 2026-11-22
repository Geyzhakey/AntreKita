<div class="min-h-screen">

    <nav class="flex items-center justify-between bg-white px-6 py-5 shadow-sm">

        <h1 class="text-2xl font-bold text-indigo-600">
            AntreKita
        </h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600">
                Logout
            </button>
        </form>

    </nav>

    <main class="mx-auto max-w-6xl px-6 py-10">

        <h2 class="text-2xl font-bold text-slate-800">
            Halo, {{ auth()->user()->name }}!
        </h2>

        <p class="mt-2 text-slate-500">
            Selamat datang di dashboard AntreKita.
        </p>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Nama pengguna</p>

                <h3 class="mt-2 text-xl font-bold text-slate-800">
                    {{ auth()->user()->name }}
                </h3>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Email</p>

                <h3 class="mt-2 break-all text-lg font-bold text-slate-800">
                    {{ auth()->user()->email }}
                </h3>
            </div>

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Status akun</p>

                <h3 class="mt-2 text-xl font-bold text-green-600">
                    Aktif
                </h3>
            </div>

        </div>

    </main>

</div>