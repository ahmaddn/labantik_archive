@extends('layouts.app')
@section('title', 'Input Kelulusan Siswa')
@section('page-title', 'Input Kelulusan')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Input Kelulusan</h1>
                <p class="text-gray-500 text-sm mt-1">Daftarkan kelulusan siswa beserta mata pelajaran yang ditempuh.</p>
            </div>
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        <form action="{{ route('admin.graduation.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Kiri: Data Utama (Informasi Surat) --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-base font-semibold text-gray-800">Informasi Surat</h3>
                        </div>

                        <div class="p-6 space-y-5">
                            {{-- Pilih Siswa --}}
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Pilih Siswa <span class="text-red-500">*</span>
                                </label>
                                <select id="user_id" name="user_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nomor Surat --}}
                            <div>
                                <label for="letter_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nomor Surat <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="letter_number" name="letter_number" required
                                    value="{{ old('letter_number') }}" placeholder="Contoh: 421/001/SMK/2026"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('letter_number') border-red-500 @enderror">
                                @error('letter_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tanggal Kelulusan --}}
                            <div>
                                <label for="graduation_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Kelulusan <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="graduation_date" name="graduation_date" required
                                    value="{{ old('graduation_date') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('graduation_date') border-red-500 @enderror">
                                @error('graduation_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Pilih Mapel dengan Accordion --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800">Daftar Mata Pelajaran</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Klik pada jurusan untuk melihat daftar mata
                                    pelajaran</p>
                            </div>
                            <span
                                class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">
                                {{ count($mapels_grouped) }} Kelompok
                            </span>
                        </div>

                        <div class="divide-y divide-gray-100">
                            @foreach ($mapels_grouped as $expertiseName => $group)
                                {{-- Accordion Item --}}
                                <div class="group/accordion flex flex-col">
                                    {{-- Header Accordion (Menggunakan Label agar bisa trigger checkbox) --}}
                                    <label for="acc-{{ Str::slug($expertiseName) }}"
                                        class="flex items-center justify-between px-6 py-4 bg-white hover:bg-gray-50 cursor-pointer transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">
                                                {{ count($group) }}
                                            </div>
                                            <span
                                                class="text-sm font-bold text-gray-700 uppercase tracking-wide">{{ $expertiseName }}</span>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-300 group-has-[:checked]/accordion:rotate-180"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </label>

                                    {{-- Checkbox Tersembunyi (Controller Accordion) --}}
                                    <input type="checkbox" id="acc-{{ Str::slug($expertiseName) }}" class="peer hidden"
                                        checked>

                                    {{-- Content Accordion --}}
                                    <div
                                        class="max-h-0 overflow-hidden transition-all duration-300 ease-in-out peer-checked:max-h-[1000px] bg-gray-50/30">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-6 pt-2">
                                            @foreach ($group as $m)
                                                <label
                                                    class="group relative flex items-start p-4 bg-white border border-gray-200 rounded-xl hover:border-blue-300 transition-all cursor-pointer shadow-sm">
                                                    <div class="flex items-center h-5">
                                                        <input name="mapel_ids[]" value="{{ $m->uuid }}"
                                                            type="checkbox"
                                                            class="h-5 w-5 text-[#1b84ff] border-gray-300 rounded-lg focus:ring-blue-500 transition-all">
                                                    </div>
                                                    <div class="ml-4 text-sm">
                                                        <span
                                                            class="block font-bold text-gray-800 group-hover:text-blue-700 transition-colors">{{ $m->name }}</span>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span
                                                                class="text-[10px] px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded font-bold uppercase">{{ $m->type }}</span>
                                                            <span
                                                                class="text-gray-400 text-xs">{{ $m->class->name }}</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('mapel_ids')
                            <div class="px-6 py-3 bg-red-50 border-t border-red-100">
                                <p class="text-red-500 text-xs font-medium">{{ $message }}</p>
                            </div>
                        @enderror

                        {{-- Footer Buttons --}}
                        <div class="px-6 py-5 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                            <a href="{{ route('admin.graduation.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200 text-sm">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-8 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-bold rounded-xl transition-all shadow-sm shadow-blue-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Data Kelulusan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
