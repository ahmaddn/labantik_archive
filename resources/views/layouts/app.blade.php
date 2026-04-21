<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Port')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-link {
            transition: all .18s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: #eff6ff;
            color: #1b84ff;
        }

        .sidebar-link.active {
            font-weight: 700;
        }

        .sidebar-link .icon {
            transition: color .18s ease;
        }

        .sidebar-link:hover .icon,
        .sidebar-link.active .icon {
            color: #1b84ff;
        }

        .dropdown-menu {
            transform-origin: top right;
            transition: opacity .15s ease, transform .15s ease;
        }

        .dropdown-menu.hidden {
            opacity: 0;
            transform: scale(.95) translateY(-4px);
            pointer-events: none;
        }

        .dropdown-menu.show {
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: auto;
        }

        .cat-body {
            transition: max-height .3s ease, opacity .3s ease;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
        }

        .cat-body.open {
            max-height: 9999px;
            opacity: 1;
        }

        .cat-chevron {
            transition: transform .25s ease;
        }

        .cat-chevron.rotated {
            transform: rotate(180deg);
        }

        #mobileSidebar {
            transition: transform .25s ease;
        }

        #sidebarOverlay {
            transition: opacity .25s ease;
        }

        /* Desktop sidebar slide */
        #desktopSidebar {
            transition: transform .25s ease, width .25s ease;
        }

        #desktopSidebar.collapsed {
            transform: translateX(-100%);
            width: 0;
            overflow: hidden;
        }

        #mainContent {
            transition: margin-left .25s ease;
        }

        /* scrollbar slim */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body class="min-h-screen bg-[#f1f5f9]">

    {{-- ────────────────────────────────────────────────
     DESKTOP SIDEBAR (fixed, always visible ≥ lg)
──────────────────────────────────────────────── --}}
    <aside id="desktopSidebar"
        class="fixed left-0 top-0 z-30 hidden h-full w-64 flex-col border-r border-gray-200 bg-white lg:flex">

        {{-- Brand + Close Button --}}
        <div class="flex items-center justify-between px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl">
                    <img src="https://smkn1talaga.sch.id/assets/images/logosmk.png" alt="Portofolio Digital"
                        class="h-full w-full object-contain">
                </div>
                <span class="text-lg font-extrabold tracking-tight text-gray-900">Portofolio Digital</span>
            </div>
            <button onclick="toggleDesktopSidebar()"
                class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700"
                title="Tutup sidebar">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Menu</p>

            <a href="{{ route('drive.index') }}"
                class="sidebar-link {{ request()->routeIs('drive.index') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                Dokumen Saya </a>

            <a href="{{ route('drive.create') }}"
                class="sidebar-link {{ request()->routeIs('drive.create') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Dokumen
            </a>

            @auth
                @if (auth()->user()->isSuperAdmin())
                    <div x-data="{ open: {{ request()->routeIs('admin.students.*', 'admin.teachers.*', 'admin.piket.*') ? 'true' : 'false' }} }">

                        <button @click="open = !open"
                            class="sidebar-link {{ request()->routeIs('admin.students.*', 'admin.teachers.*', 'admin.piket.*') ? 'active' : '' }} flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                            <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="flex-1 text-left">Data Pengguna</span>
                            <svg class="h-4 w-4 flex-shrink-0 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="mt-0.5 space-y-0.5 pl-4">

                            <a href="{{ route('admin.students.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Data Siswa
                            </a>

                            <a href="{{ route('admin.teachers.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Data Guru
                            </a>

                            <a href="{{ route('admin.piket.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.piket.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Data Guru TU
                            </a>

                        </div>
                    </div>




                    <a href="{{ route('admin.categories.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                        <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Kategori
                    </a>

                    <a href="{{ route('admin.history.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.history.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                        <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History Upload
                    </a>
                    <a href="{{ route('admin.google.connect') }}"
                        class="sidebar-link {{ request()->routeIs('admin.google.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                        <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z" />
                        </svg>
                        Google Drive
                    </a>
                @endif
            @endauth
        </nav>

        {{-- Profile Footer --}}
        @auth
            <div class="border-t border-gray-200 px-4 py-4">
                <a href="{{ route('profile.edit') }}"
                    class="group flex items-center gap-3 rounded-xl px-2 py-2 transition-colors hover:bg-gray-50">
                    <div
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#1b84ff] to-[#0ea5e9] text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 flex-shrink-0 text-gray-300 transition-colors group-hover:text-gray-500"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endauth
    </aside>

    {{-- ────────────────────────────────────────────────
     MOBILE SIDEBAR (slide in)
──────────────────────────────────────────────── --}}
    <div id="sidebarOverlay" class="pointer-events-none fixed inset-0 z-40 bg-black/50 opacity-0 lg:hidden"
        onclick="closeSidebar()"></div>

    <aside id="mobileSidebar"
        class="fixed left-0 top-0 z-50 flex h-full w-64 -translate-x-full flex-col border-r border-gray-200 bg-white lg:hidden">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center overflow-hidden rounded-xl">
                    <img src="https://smkn1talaga.sch.id/assets/images/logosmk.png" alt="Portofolio Digital"
                        class="h-full w-full object-contain">
                </div>
                <span class="text-lg font-extrabold tracking-tight text-gray-900">Portofolio Digital</span>
            </div>
            <button onclick="closeSidebar()"
                class="rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700"
                title="Tutup sidebar">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5">
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Menu</p>

            <a href="{{ route('drive.index') }}"
                class="sidebar-link {{ request()->routeIs('drive.index') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600"
                onclick="closeSidebar()">
                <svg class="icon h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                Dokumen Saya
            </a>

            <a href="{{ route('drive.create') }}"
                class="sidebar-link {{ request()->routeIs('drive.create') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600"
                onclick="closeSidebar()">
                <svg class="icon h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload Dokumen
            </a>

            @auth
                @if (auth()->user()->isSuperAdmin())
                    <div x-data="{ open: {{ request()->routeIs('admin.students.*', 'admin.teachers.*', 'admin.piket.*') ? 'true' : 'false' }} }">

                        <button @click="open = !open"
                            class="sidebar-link {{ request()->routeIs('admin.students.*', 'admin.teachers.*', 'admin.piket.*') ? 'active' : '' }} flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                            <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="flex-1 text-left">Data Pengguna</span>
                            <svg class="h-4 w-4 flex-shrink-0 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="mt-0.5 space-y-0.5 pl-4">

                            <a href="{{ route('admin.students.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Data Siswa
                            </a>

                            <a href="{{ route('admin.teachers.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.teachers.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Data Guru
                            </a>

                            <a href="{{ route('admin.piket.index') }}"
                                class="sidebar-link {{ request()->routeIs('admin.piket.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600">
                                <svg class="icon h-5 w-5 flex-shrink-0 text-gray-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Data Guru TU
                            </a>

                        </div>
                    </div>
                    <div class="pt-4">
                        <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-widest text-gray-400">Admin</p>
                    </div>
                    <a href="{{ route('admin.google.connect') }}"
                        class="sidebar-link flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600"
                        onclick="closeSidebar()">
                        <svg class="icon h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z" />
                        </svg>
                        Google Drive
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600"
                        onclick="closeSidebar()">
                        <svg class="icon h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Kategori
                    </a>

                    <a href="{{ route('admin.history.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.history.*') ? 'active' : '' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-gray-600"
                        onclick="closeSidebar()">
                        <svg class="icon h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History Upload
                    </a>
                @endif
            @endauth
        </nav>

        @auth
            <div class="border-t border-gray-200 px-4 py-4">
                <a href="{{ route('profile.edit') }}"
                    class="group flex items-center gap-3 rounded-xl px-2 py-2 transition-colors hover:bg-gray-50">
                    <div
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-[#1b84ff] to-[#0ea5e9] text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs text-gray-400">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="ml-auto h-4 w-4 flex-shrink-0 text-gray-300 transition-colors group-hover:text-gray-500"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        @endauth
    </aside>

    {{-- ────────────────────────────────────────────────
     MAIN AREA
──────────────────────────────────────────────── --}}
    <div id="mainContent" class="flex min-h-screen flex-col lg:ml-64">

        {{-- TOPBAR --}}
        <header class="sticky top-0 z-20 border-b border-gray-200 bg-white shadow-sm">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6">

                {{-- Left: Hamburger + Page Title --}}
                <div class="flex items-center gap-3">
                    {{-- Mobile hamburger --}}
                    <button onclick="openSidebar()"
                        class="rounded-xl p-2 text-gray-500 transition-colors hover:bg-gray-100 lg:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    {{-- Desktop sidebar toggle --}}
                    <button onclick="toggleDesktopSidebar()" id="desktopToggleBtn"
                        class="hidden rounded-xl p-2 text-gray-500 transition-colors hover:bg-gray-100 lg:flex">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    {{-- Mobile Brand --}}
                    <div class="flex items-center gap-2 lg:hidden">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-[#1b84ff]">
                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                        </div>
                        <span class="text-base font-extrabold text-gray-800">Portofolio Digital</span>
                    </div>
                    {{-- Page title desktop --}}
                    <h1 class="hidden text-base font-semibold text-gray-700 lg:block">@yield('page-title', 'Dashboard')</h1>
                </div>

                {{-- Right: Notifications + Profile --}}
                <div class="flex items-center gap-2">

                    @auth
                        {{-- Profile Dropdown --}}
                        <div class="relative" id="profileDropdownWrapper">
                            <button onclick="toggleDropdown()"
                                class="group flex items-center gap-2.5 rounded-xl py-1.5 pl-2 pr-3 transition-colors hover:bg-gray-100">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-[#1b84ff] to-[#0ea5e9] text-xs font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden text-left sm:block">
                                    <p class="text-sm font-semibold leading-tight text-gray-800">
                                        {{ auth()->user()->name }}</p>
                                    @if (auth()->user()->isSuperAdmin())
                                        <p class="text-xs font-medium text-purple-600">Super Admin</p>
                                    @else
                                        <p class="text-xs text-gray-400">Pengguna</p>
                                    @endif
                                </div>
                                <svg class="hidden h-4 w-4 text-gray-400 transition-transform group-hover:text-gray-600 sm:block"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div id="profileDropdown"
                                class="dropdown-menu absolute right-0 z-50 mt-2 hidden w-56 rounded-2xl border border-gray-100 bg-white py-2 shadow-xl">

                                {{-- User Info Header --}}
                                <div class="border-b border-gray-100 px-4 py-3">
                                    <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="mt-0.5 truncate text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Edit Profil
                                </a>

                                <div class="mt-1 border-t border-gray-100 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex w-full items-center gap-3 rounded-b-xl px-4 py-2.5 text-sm text-red-600 transition-colors hover:bg-red-50">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Flash Messages (global) --}}
        @if (session('success') || session('error') || session('warning'))
            <div class="space-y-2 px-4 pt-4 sm:px-6">
                @if (session('success'))
                    <div
                        class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                        <svg class="h-5 w-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                        <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('warning'))
                    <div
                        class="flex items-center gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm text-yellow-800">
                        <svg class="h-5 w-5 flex-shrink-0 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ session('warning') }}
                    </div>
                @endif
            </div>
        @endif

        {{-- Content --}}
        <main class="flex-1 px-4 py-6 sm:px-6">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200 px-6 py-4 text-center text-xs text-gray-400">
            © {{ date('Y') }} Labantik. All rights reserved.
        </footer>
    </div>

    <script>
        // Desktop sidebar toggle
        let desktopSidebarOpen = true;

        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('desktopSidebar');
            const main = document.getElementById('mainContent');
            desktopSidebarOpen = !desktopSidebarOpen;
            if (desktopSidebarOpen) {
                sidebar.classList.remove('collapsed');
                main.style.marginLeft = '';
            } else {
                sidebar.classList.add('collapsed');
                main.style.marginLeft = '0';
            }
        }

        // Mobile Sidebar toggle
        function openSidebar() {
            document.getElementById('mobileSidebar').style.transform = 'translateX(0)';
            const overlay = document.getElementById('sidebarOverlay');
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'auto';
        }

        function closeSidebar() {
            document.getElementById('mobileSidebar').style.transform = 'translateX(-100%)';
            const overlay = document.getElementById('sidebarOverlay');
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
        }

        // Profile dropdown
        function toggleDropdown() {
            const d = document.getElementById('profileDropdown');
            if (d.classList.contains('hidden')) {
                d.classList.remove('hidden');
                setTimeout(() => d.classList.add('show'), 10);
            } else {
                d.classList.remove('show');
                setTimeout(() => d.classList.add('hidden'), 150);
            }
        }

        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('profileDropdownWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                const d = document.getElementById('profileDropdown');
                if (d && !d.classList.contains('hidden')) {
                    d.classList.remove('show');
                    setTimeout(() => d.classList.add('hidden'), 150);
                }
            }
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>

</html>
