<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Portofolio Digital</title>
    <link rel="icon" href="https://smkn1talaga.sch.id/assets/images/logosmk.png" type="image/png">
    <link rel="apple-touch-icon" href="https://smkn1talaga.sch.id/assets/images/logosmk.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-4">

    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-xl">
            {{-- Logo --}}
            <div class="mb-8 text-center">
                <div
                    class="mx-auto mb-4 flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl bg-white">
                    <img src="https://smkn1talaga.sch.id/assets/images/logosmk.png" alt="Portofolio Digital"
                        class="h-full w-full object-contain">
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
{{-- Account Picker Modal --}}
@if(session('account_candidates'))
<div id="accountModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
        <h2 class="mb-1 text-base font-semibold text-gray-900">Pilih Akun</h2>
        <p class="mb-4 text-xs text-gray-500">Ditemukan beberapa akun dengan email yang sama. Pilih akun yang ingin digunakan.</p>

        <form method="POST" action="{{ route('login.select') }}" class="space-y-3">
            @csrf
            @foreach(session('account_candidates') as $account)
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-3 transition hover:border-blue-400 hover:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                <input type="radio" name="user_id" value="{{ $account['id'] }}" class="accent-blue-600" required>
                <div class="flex flex-1 items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $account['name'] }}</p>
                        @if($account['connected'])
                            <span class="inline-block rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                ✓ Terhubung sebagai {{ $account['connected'] }}
                            </span>
                        @else
                            <span class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-400">
                                Tidak terhubung
                            </span>
                        @endif
                    </div>
                </div>
            </label>
            @endforeach

            <button type="submit"
                class="mt-2 w-full rounded-xl bg-blue-600 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                OK
            </button>
        </form>
    </div>
</div>
@endif
</body>

</html>
