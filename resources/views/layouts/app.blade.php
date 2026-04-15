<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EAGE Drive')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

{{-- Navbar --}}
<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            </div>
            <span class="font-semibold text-gray-800 text-lg">EAGE Drive</span>
        </div>

        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-500 hidden sm:block">
                    {{ auth()->user()->name }}
                    @if(auth()->user()->isSuperAdmin())
                        <span class="ml-1 px-2 py-0.5 bg-purple-100 text-purple-700 text-xs rounded-full font-medium">Super Admin</span>
                    @endif
                </span>
                <a href="{{ route('drive.index') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                    My Drive
                    </a>

                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('admin.google.connect') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Google Drive
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-sm px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

{{-- Flash Messages --}}
<div class="max-w-6xl mx-auto px-4 pt-4 space-y-2">
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl text-sm">
            ⚠️ {{ session('warning') }}
        </div>
    @endif
</div>

{{-- Content --}}
<main class="max-w-6xl mx-auto px-4 py-6">
    @yield('content')
</main>

</body>
</html>
