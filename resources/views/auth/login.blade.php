<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — EAGE Drive</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 flex items-center justify-center p-4">

<div class="w-full max-w-md">
    {{-- Card --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">EAGE Drive</h1>
            <p class="text-gray-500 text-sm mt-1">Masuk untuk melanjutkan</p>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="email@contoh.com"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                              @error('email') border-red-400 @enderror">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                <input type="password" name="password" required
                       placeholder="••••••••"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember"
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold
                           rounded-xl transition-colors shadow-sm text-sm">
                Masuk
            </button>
        </form>
    </div>
</div>

</body>
</html>
