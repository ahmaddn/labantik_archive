<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Portofolio Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4">

    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-xl">
            {{-- Logo --}}
            <div class="mb-8 text-center">
                <div
                    class="mx-auto mb-4 flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-lg">
                    <img src="https://smkn1talaga.sch.id/assets/images/logosmk.png" alt="Portofolio Digital"
                        class="h-full w-full object-contain p-1">
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Portofolio Digital</h1>
                <p class="mt-1 text-sm text-gray-500">Masuk untuk melanjutkan</p>
            </div>

            {{-- Error --}}
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="email@contoh.com"
                        class="@error('email') border-red-400 @enderror w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="h-4 w-4 rounded border-gray-300 text-blue-600">
                    <label for="remember" class="text-sm text-gray-600">Ingat saya</label>
                </div>

                <button type="submit"
                    class="w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700">
                    Masuk
                </button>
            </form>
        </div>
    </div>

</body>

</html>
