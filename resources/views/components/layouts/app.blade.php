@php
    $globalAnnouncement = \App\Models\SystemSetting::getValue('global_announcement');
    $maintenanceMode = \App\Models\SystemSetting::getValue('booking_maintenance_mode', '0') === '1';
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full antialiased scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AntreKita — Smart Spatial Queue' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">

    <script>
        (function () {
            const saved = localStorage.getItem('antrekita-theme');
            document.documentElement.classList.toggle('dark', saved === 'dark');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-app-canvas flex min-h-full flex-col font-sans text-[#0E241E] transition-colors duration-200 selection:bg-[#90DEC3] selection:text-[#0E241E] dark:text-[#E6F2EE]">
    @if ($maintenanceMode || filled($globalAnnouncement))
        <div class="relative z-50 border-b border-[#193B32] bg-[#0E241E] px-4 py-2 text-center text-[11px] font-medium text-[#E6F2EE] dark:border-[#213531] dark:bg-[#152623]">
            @if ($maintenanceMode)
                <span class="mr-2 rounded bg-[#90DEC3] px-2 py-0.5 font-mono text-[10px] font-bold text-[#0E241E]">PEMELIHARAAN</span>
                Reservasi baru sedang dijeda sementara oleh pengelola sistem.
            @endif
            @if (filled($globalAnnouncement))
                <span class="ml-1 font-semibold text-[#90DEC3]">{{ $globalAnnouncement }}</span>
            @endif
        </div>
    @endif

    <header class="glass-nav sticky top-0 z-40">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" wire:navigate class="group flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0F6E56] text-[#90DEC3] shadow-xs transition group-hover:scale-[1.03] dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <rect x="3" y="3" width="7" height="7" rx="1.5" />
                        <rect x="14" y="3" width="7" height="7" rx="1.5" />
                        <rect x="3" y="14" width="7" height="7" rx="1.5" />
                        <path d="M14 17.5h7M17.5 14v7" stroke-linecap="round" />
                    </svg>
                </span>
                <span class="text-base font-extrabold tracking-tight">AntreKita</span>
            </a>

            @auth
                <nav class="hidden items-center gap-1 md:flex">
                    @if (auth()->user()->isDeveloper())
                        <a href="{{ route('developer.console') }}" wire:navigate
                            class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ request()->routeIs('developer.*') ? 'bg-[#90DEC3]/65 text-[#0E241E] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC] dark:hover:text-[#E6F2EE]' }}">
                            Pusat Kendali
                        </a>
                    @elseif (auth()->user()->isAdmin())
                        <a href="{{ route('admin.monitor') }}" wire:navigate
                            class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ request()->routeIs('admin.monitor') ? 'bg-[#90DEC3]/65 text-[#0E241E] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC] dark:hover:text-[#E6F2EE]' }}">
                            Live Monitor
                        </a>
                        <a href="{{ route('admin.setup') }}" wire:navigate
                            class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ request()->routeIs('admin.setup') ? 'bg-[#90DEC3]/65 text-[#0E241E] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC] dark:hover:text-[#E6F2EE]' }}">
                            Peta &amp; Unit
                        </a>
                    @else
                        <a href="{{ route('visitor.institutions') }}" wire:navigate
                            class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ request()->routeIs('visitor.institutions', 'visitor.map') ? 'bg-[#90DEC3]/65 text-[#0E241E] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC] dark:hover:text-[#E6F2EE]' }}">
                            Jelajah Fasilitas
                        </a>
                        <a href="{{ route('visitor.session') }}" wire:navigate
                            class="rounded-lg px-3.5 py-1.5 text-xs font-semibold transition {{ request()->routeIs('visitor.session') ? 'bg-[#90DEC3]/65 text-[#0E241E] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] hover:text-[#0E241E] dark:text-[#98B4AC] dark:hover:text-[#E6F2EE]' }}">
                            Sesi &amp; Antrean
                        </a>
                    @endif
                </nav>
            @else
                <nav class="hidden items-center gap-6 text-xs font-semibold text-[#47625A] md:flex dark:text-[#98B4AC]">
                    <a href="{{ route('home') }}#fasilitas" class="transition hover:text-[#0F6E56] dark:hover:text-[#90DEC3]">Fasilitas</a>
                    <a href="{{ route('home') }}#cara-kerja" class="transition hover:text-[#0F6E56] dark:hover:text-[#90DEC3]">Cara Kerja</a>
                    <a href="{{ route('login.admin') }}" wire:navigate class="transition hover:text-[#0F6E56] dark:hover:text-[#90DEC3]">Portal Pengelola</a>
                </nav>
            @endauth

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-data="{ dark: document.documentElement.classList.contains('dark') }"
                    @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.setItem('antrekita-theme', dark ? 'dark' : 'light');"
                    class="surface-subtle inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-[11px] font-semibold text-[#0E241E] transition hover:border-[#0F6E56]/50 dark:text-[#90DEC3] dark:hover:border-[#90DEC3]/50"
                    title="Ganti Tema Light / Dark"
                >
                    <svg x-show="!dark" class="h-3.5 w-3.5 text-[#0F6E56]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" stroke-linecap="round" />
                    </svg>
                    <svg x-show="dark" x-cloak class="h-3.5 w-3.5 text-[#90DEC3]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span x-show="!dark">Light</span>
                    <span x-show="dark" x-cloak>Dark</span>
                </button>

                @auth
                    @if (auth()->user()->isVisitor())
                        <livewire:visitor.notification-watcher />
                    @endif

                    <div class="surface-subtle hidden items-center gap-2 rounded-lg py-1 pr-3 pl-1.5 sm:flex">
                        <span class="flex h-6 w-6 items-center justify-center rounded-md bg-[#0F6E56] font-mono text-[10px] font-bold text-[#90DEC3] dark:bg-[#90DEC3] dark:text-[#0B1F19]">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <div class="leading-tight">
                            <div class="text-[11px] font-bold">{{ auth()->user()->name }}</div>
                            <div class="font-mono text-[9.5px] text-[#47625A] dark:text-[#98B4AC]">
                                {{ auth()->user()->isAdmin() && auth()->user()->registration_code ? auth()->user()->registration_code : (auth()->user()->affiliation ?? auth()->user()->username) }}
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="surface-subtle rounded-lg px-3.5 py-1.5 text-xs font-semibold text-[#0E241E] transition hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700 dark:text-[#E6F2EE] dark:hover:border-rose-800 dark:hover:bg-rose-950/60 dark:hover:text-rose-300">
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" wire:navigate
                        class="btn-brand rounded-lg px-4 py-2 text-xs font-semibold shadow-xs">
                        Masuk / Daftar
                    </a>
                @endauth
            </div>
        </div>

        @auth
            <div class="flex items-center justify-center gap-1 border-t border-[#CBE8DC]/80 px-4 py-1.5 md:hidden dark:border-[#213531]">
                @if (auth()->user()->isDeveloper())
                    <a href="{{ route('developer.console') }}" wire:navigate
                        class="rounded-lg px-3 py-1 text-[11px] font-semibold {{ request()->routeIs('developer.*') ? 'bg-[#0F6E56] text-[#F7FCFA] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                        Pusat Kendali
                    </a>
                @elseif (auth()->user()->isAdmin())
                    <a href="{{ route('admin.monitor') }}" wire:navigate
                        class="rounded-lg px-3 py-1 text-[11px] font-semibold {{ request()->routeIs('admin.monitor') ? 'bg-[#0F6E56] text-[#F7FCFA] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                        Live Monitor
                    </a>
                    <a href="{{ route('admin.setup') }}" wire:navigate
                        class="rounded-lg px-3 py-1 text-[11px] font-semibold {{ request()->routeIs('admin.setup') ? 'bg-[#0F6E56] text-[#F7FCFA] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                        Peta &amp; Unit
                    </a>
                @else
                    <a href="{{ route('visitor.institutions') }}" wire:navigate
                        class="rounded-lg px-3 py-1 text-[11px] font-semibold {{ request()->routeIs('visitor.institutions', 'visitor.map') ? 'bg-[#0F6E56] text-[#F7FCFA] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                        Jelajah Fasilitas
                    </a>
                    <a href="{{ route('visitor.session') }}" wire:navigate
                        class="rounded-lg px-3 py-1 text-[11px] font-semibold {{ request()->routeIs('visitor.session') ? 'bg-[#0F6E56] text-[#F7FCFA] dark:bg-[#90DEC3] dark:text-[#0B1F19]' : 'text-[#47625A] dark:text-[#98B4AC]' }}">
                        Sesi &amp; Antrean
                    </a>
                @endif
            </div>
        @endauth
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>

    <footer class="border-t border-[#CBE8DC] py-6 text-xs text-[#47625A] dark:border-[#213531] dark:text-[#98B4AC]">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
            <span class="font-semibold text-[#0E241E] dark:text-[#E6F2EE]">AntreKita</span>
            <span>Sistem Antrean Ruang Belajar Terbuka &middot; Web-First &amp; Real-Time</span>
        </div>
    </footer>

    <x-ui.whatsapp-toast />

    @livewireScripts
</body>

</html>
