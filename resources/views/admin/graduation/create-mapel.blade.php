@extends('layouts.app')
@section('title', 'Tambah Mapel')
@section('page-title', 'Tambah Mapel')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">Tambah Mapel</h1>
                <p class="text-gray-500 text-sm mt-1">Tambahkan mata pelajaran baru untuk kelulusan siswa.</p>
            </div>
            <a href="{{ route('admin.graduation.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors text-sm shadow-sm border border-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">Form Tambah Mapel</h2>
            </div>

            <form method="POST" action="{{ route('admin.graduation.storeMapel') }}" class="p-6 space-y-5">
                @csrf

                {{-- Kelas --}}
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kelas <span class="text-red-500">*</span>
                    </label>
                    <select id="class_id" name="class_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('class_id') border-red-500 @enderror">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>
                                {{ $class->academic_level }} {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jurusan/Expertise --}}
                <div>
                    <label for="expertise_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Jurusan/Konsentrasi Keahlian <span class="text-red-500">*</span>
                    </label>
                    <select id="expertise_id" name="expertise_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expertise_id') border-red-500 @enderror">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach ($expertise as $exp)
                            <option value="{{ $exp->id }}" @selected(old('expertise_id') == $exp->id)>
                                {{ $exp->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expertise_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Mapel --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Mapel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                        placeholder="Contoh: Matematika, Bahasa Indonesia, dll" value="{{ old('name') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipe Mapel --}}
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Mapel <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="wajib" @selected(old('type') == 'wajib')>Wajib</option>
                        <option value="pilihan" @selected(old('type') == 'pilihan')>Pilihan</option>
                        <option value="praktik" @selected(old('type') == 'praktik')>Praktik</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl transition-colors shadow-sm shadow-blue-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Simpan Mapel
                    </button>
                    <a href="{{ route('admin.graduation.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl transition-colors border border-gray-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
