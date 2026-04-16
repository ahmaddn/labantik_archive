@extends('layouts.app')
@section('title', 'Edit Profil')
@section('page-title', 'Profil Saya')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">

        {{-- Profile Header Card --}}
        <div class="bg-gradient-to-br from-[#0f172a] via-[#1e3a5f] to-[#1b84ff] rounded-2xl p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full translate-y-20 -translate-x-10"></div>
            <div class="relative flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-3xl font-extrabold text-white shadow-lg flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-xl font-extrabold">{{ auth()->user()->name }}</h2>
                    <p class="text-blue-200 text-sm mt-0.5">{{ auth()->user()->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        @if(auth()->user()->isSuperAdmin())
                            <span class="px-2.5 py-0.5 bg-purple-500/30 text-purple-200 text-xs font-semibold rounded-full border border-purple-400/30">
                                Super Admin
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 bg-blue-500/30 text-blue-200 text-xs font-semibold rounded-full border border-blue-400/30">
                                Pengguna
                            </span>
                        @endif
                        <span class="text-xs text-blue-300">Bergabung {{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Edit Info Pribadi ─────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#1b84ff]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800">Informasi Pribadi</h3>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', auth()->user()->name) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                  focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                  @error('name') border-red-400 bg-red-50 @enderror"
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', auth()->user()->email) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm
                                  focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                  @error('email') border-red-400 bg-red-50 @enderror"
                           required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Ganti Password ────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-800">Ganti Password</h3>
            </div>
            <form method="POST" action="{{ route('profile.password') }}" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                {{-- Password Lama --}}
                <div>
                    <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm pr-11
                                      focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                      @error('current_password') border-red-400 bg-red-50 @enderror">
                        <button type="button" onclick="togglePwd('current_password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm pr-11
                                      focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors
                                      @error('password') border-red-400 bg-red-50 @enderror"
                               oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePwd('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Strength bar --}}
                    <div class="mt-2">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div id="strengthBar" class="h-full rounded-full transition-all duration-300 w-0"></div>
                        </div>
                        <p id="strengthText" class="text-xs text-gray-400 mt-1"></p>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Konfirmasi Password Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm pr-11
                                      focus:ring-2 focus:ring-[#1b84ff]/30 focus:border-[#1b84ff] transition-colors">
                        <button type="button" onclick="togglePwd('password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm shadow-orange-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        function togglePwd(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function checkStrength(val) {
            const bar  = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            let score  = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;
            const configs = [
                { w: '0%',   color: '',               label: '' },
                { w: '25%',  color: 'bg-red-500',     label: 'Lemah' },
                { w: '50%',  color: 'bg-orange-400',  label: 'Cukup' },
                { w: '75%',  color: 'bg-yellow-400',  label: 'Baik' },
                { w: '100%', color: 'bg-green-500',   label: 'Kuat' },
            ];
            const c = configs[score] || configs[0];
            bar.style.width  = val.length ? c.w : '0%';
            bar.className    = 'h-full rounded-full transition-all duration-300 ' + c.color;
            text.textContent = val.length ? c.label : '';
        }
    </script>
@endsection
