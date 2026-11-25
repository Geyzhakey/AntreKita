@php
    $globalAnnouncement = \App\Models\SystemSetting::getValue('global_announcement');
    $maintenanceMode = \App\Models\SystemSetting::getValue('booking_maintenance_mode', '0') === '1';
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AntreKita — Smart Spatial Library Queue' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Newsreader:ital,opsz,wght@0,6..72,500;0,6..72,600;0,6..72,700;1,6..72,500&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        (function () {
            const saved = localStorage.getItem('antrekita-theme');
            document.documentElement.classList.toggle('dark', saved === 'dark');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-library-motif relative min-h-full font-sans text-[#1A241E] transition-colors duration-200 selection:bg-[#CAFFA1] selection:text-[#1A241E] dark:text-[#F4EFE2]">
    <!-- Subtle Architectural Botanical Corner Ornaments -->
    <svg class="pointer-events-none fixed top-20 left-4 z-0 hidden h-28 w-28 text-[#14452F]/10 lg:block dark:text-[#CAFFA1]/10" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2">
        <path d="M10 110V26c0-8.8 7.2-16 16-16h84" />
        <path d="M22 68c0-25 18-46 46-46-2 26-20 46-46 46z" />
        <path d="M22 68c16-16 32-30 46-46" />
    </svg>
    <svg class="pointer-events-none fixed right-4 bottom-6 z-0 hidden h-28 w-28 rotate-180 text-[#14452F]/10 lg:block dark:text-[#CAFFA1]/10" viewBox="0 0 120 120" fill="none" stroke="currentColor" stroke-width="1.2">
        <path d="M10 110V26c0-8.8 7.2-16 16-16h84" />
        <path d="M22 68c0-25 18-46 46-46-2 26-20 46-46 46z" />
        <path d="M22 68c16-16 32-30 46-46" />
    </svg>

    @if ($maintenanceMode || filled($globalAnnouncement))
        <div class="relative z-40 border-b border-[#D9D1BA] bg-[#CAFFA1] px-4 py-2 text-center text-xs font-bold text-[#1A241E] dark:border-[#27382F] dark:bg-[#1C2922] dark:text-[#CAFFA1]">
            @if ($maintenanceMode)
                <span class="mr-2 rounded bg-[#14452F] px-2 py-0.5 font-mono text-[10px] text-[#CAFFA1]">PEMELIHARAAN</span>
                Reservasi baru sedang dijeda sementara oleh pengelola sistem AntreKita.
            @endif
            @if (filled($globalAnnouncement))
                <span class="ml-1">{{ $globalAnnouncement }}</span>
            @endif
        </div>
    @endif

    <div class="relative z-30 mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8">
        <header class="paper-card flex h-16 items-center justify-between gap-4 rounded-2xl px-4 backdrop-blur-md sm:px-6">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl border border-[#14452F]/20 bg-[#CAFFA1] text-[#14452F] dark:border-[#CAFFA1]/30 dark:bg-[#1C2922] dark:text-[#CAFFA1]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path d="M2 6s4-2 10 0c6-2 10 0 10 0v13s-4-2-10 0c-6-2-10 0-10 0V6z" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M12 6v13" stroke-linecap="round" />
                    </svg>
                </span>
                <span class="flex flex-col leading-none">
                    <span class="font-serif text-xl font-bold tracking-tight">AntreKita</span>
                    <span class="font-mono text-[10px] font-semibold uppercase tracking-widest text-[#4E5C52] dark:text-[#9CB09E]">Spatial Library Queue</span>
                </span>
            </a>

            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <nav class="hidden items-center gap-1 md:flex">
                        @if (auth()->user()->isDeveloper())
                            <a href="{{ route('developer.console') }}" wire:navigate
                                class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request()->routeIs('developer.*') ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] hover:bg-[#EFEAD8] dark:text-[#B5C4B6] dark:hover:bg-[#1C2922]' }}">
                                Pusat Kendali
                            </a>
                        @elseif (auth()->user()->isAdmin())
                            <a href="{{ route('admin.monitor') }}" wire:navigate
                                class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request()->routeIs('admin.monitor') ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] hover:bg-[#EFEAD8] dark:text-[#B5C4B6] dark:hover:bg-[#1C2922]' }}">
                                Live Monitor
                            </a>
                            <a href="{{ route('admin.setup') }}" wire:navigate
                                class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request()->routeIs('admin.setup') ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] hover:bg-[#EFEAD8] dark:text-[#B5C4B6] dark:hover:bg-[#1C2922]' }}">
                                Peta &amp; Unit
                            </a>
                        @else
                            <a href="{{ route('visitor.institutions') }}" wire:navigate
                                class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request()->routeIs('visitor.institutions', 'visitor.map') ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] hover:bg-[#EFEAD8] dark:text-[#B5C4B6] dark:hover:bg-[#1C2922]' }}">
                                Jelajah Fasilitas
                            </a>
                            <a href="{{ route('visitor.session') }}" wire:navigate
                                class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ request()->routeIs('visitor.session') ? 'bg-[#CAFFA1] text-[#1A241E]' : 'text-[#4E5C52] hover:bg-[#EFEAD8] dark:text-[#B5C4B6] dark:hover:bg-[#1C2922]' }}">
                                Sesi &amp; Antrean
                            </a>
                        @endif
                    </nav>
                @endauth

                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('antrekita-theme', dark ? 'dark' : 'light');"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] px-3 py-1.5 font-mono text-[11px] font-bold text-[#1A241E] transition hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922] dark:text-[#CAFFA1]"
                    title="Ganti Tema Light / Dark"
                >
                    <svg x-show="!dark" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" stroke-linecap="round" />
                    </svg>
                    <svg x-show="dark" x-cloak class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span x-show="!dark">Light</span>
                    <span x-show="dark" x-cloak>Dark</span>
                </button>

                @auth
                    <div class="hidden text-right sm:block">
                        <div class="text-xs font-bold">{{ auth()->user()->name }}</div>
                        <div class="font-mono text-[10px] text-[#4E5C52] dark:text-[#9CB09E]">
                            {{ auth()->user()->isAdmin() && auth()->user()->registration_code ? auth()->user()->registration_code : (auth()->user()->affiliation ?? auth()->user()->username) }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] px-3 py-1.5 text-xs font-bold text-[#1A241E] transition hover:bg-rose-100 hover:text-rose-900 dark:border-[#27382F] dark:bg-[#1C2922] dark:text-[#F4EFE2] dark:hover:bg-rose-950/60 dark:hover:text-rose-300">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                        class="rounded-xl border border-[#D9D1BA] bg-[#EFEAD8] px-3.5 py-1.5 text-xs font-bold text-[#1A241E] transition hover:border-[#14452F] dark:border-[#27382F] dark:bg-[#1C2922] dark:text-[#F4EFE2]">
                        Portal Masuk
                    </a>
                    <a href="{{ route('register.visitor') }}" wire:navigate
                        class="rounded-xl bg-[#14452F] px-3.5 py-1.5 text-xs font-bold text-[#CAFFA1] transition hover:bg-[#0F3524] dark:bg-[#CAFFA1] dark:text-[#1A241E]">
                        Daftar Visitor
                    </a>
                @endauth
            </div>
        </header>
    </div>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-7 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <x-ui.whatsapp-toast />

    @livewireScripts
</body>

</html>
