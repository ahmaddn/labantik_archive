@extends('layouts.app')
@section('title', 'Edit Sertifikat')
@section('page-title', 'Edit Sertifikat')

@section('styles')
    <!-- Select2 CSS & Summernote / Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@700&family=Dancing+Script:wght@600&family=Great+Vibes&family=Montserrat:wght@500;700&family=Pacifico&family=Playfair+Display:ital,wght@0,600;1,400&family=Sacramento&display=swap" rel="stylesheet">
    <style>
        /* Select2 Modern & Clean Theme Styling */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            height: 44px !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 12px !important;
            transition: all 0.15s ease-in-out;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1f2937 !important;
            font-size: 0.875rem !important;
            line-height: normal !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }
        .select2-container--default .select2-selection--multiple {
            background-color: #f9fafb !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            min-height: 44px !important;
            padding: 4px 8px !important;
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 4px !important;
            transition: all 0.15s ease-in-out;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #1d4ed8 !important;
            border-radius: 0.5rem !important;
            padding: 3px 8px !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            margin: 2px 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            float: none !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #3b82f6 !important;
            margin-right: 5px !important;
            border: none !important;
            font-weight: bold !important;
            float: none !important;
            position: relative !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #1e40af !important;
            background-color: transparent !important;
        }
        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 0 !important;
            height: 32px !important;
            font-size: 0.875rem !important;
            font-family: inherit !important;
            color: #374151 !important;
        }
        .select2-dropdown {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
            font-size: 0.875rem !important;
            z-index: 99999 !important;
            background-color: #ffffff !important;
        }
        .select2-results__option {
            padding: 8px 14px !important;
            font-size: 0.875rem !important;
            color: #374151 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
        }
        .select2-search__field {
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            padding: 8px 12px !important;
            outline: none !important;
            font-size: 0.875rem !important;
        }

        /* Quill fontpicker styling */
        .ql-font-great-vibes { font-family: 'Great Vibes', cursive; }
        .ql-font-dancing-script { font-family: 'Dancing Script', cursive; }
        .ql-font-alex-brush { font-family: 'Alex Brush', cursive; }
        .ql-font-sacramento { font-family: 'Sacramento', cursive; }
        .ql-font-pacifico { font-family: 'Pacifico', cursive; }
        .ql-font-playfair { font-family: 'Playfair Display', serif; }
        .ql-font-cinzel { font-family: 'Cinzel', serif; }
        .ql-font-tahoma { font-family: 'Tahoma', sans-serif; }

        /* Quill Picker Dropdown Item Labels & Font Previews */
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="great-vibes"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="great-vibes"]::before { content: "Great Vibes"; font-family: 'Great Vibes', cursive; font-size: 16px; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="dancing-script"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="dancing-script"]::before { content: "Dancing Script"; font-family: 'Dancing Script', cursive; font-size: 15px; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="alex-brush"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="alex-brush"]::before { content: "Alex Brush"; font-family: 'Alex Brush', cursive; font-size: 16px; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="sacramento"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="sacramento"]::before { content: "Sacramento"; font-family: 'Sacramento', cursive; font-size: 16px; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="pacifico"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="pacifico"]::before { content: "Pacifico"; font-family: 'Pacifico', cursive; font-size: 14px; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="playfair"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="playfair"]::before { content: "Playfair Display"; font-family: 'Playfair Display', serif; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="cinzel"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="cinzel"]::before { content: "Cinzel"; font-family: 'Cinzel', serif; }

        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="tahoma"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="tahoma"]::before { content: "Tahoma"; font-family: 'Tahoma', sans-serif; }

        /* Quill Picker Dropdown Scrollbar Fix */
        .ql-snow .ql-picker-options {
            max-height: 220px !important;
            overflow-y: auto !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border-radius: 0.5rem !important;
            padding: 4px 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Edit Sertifikat: {{ $certificate->title }}</h1>
            <p class="text-gray-500 text-sm mt-1">Perbarui konfigurasi sertifikat, penandatangan, dan susunan materi.</p>
        </div>
        <a href="{{ route('admin.certificates.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition-colors w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm">
            <div class="font-bold mb-1">Terjadi kesalahan pada input data:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.certificates.update', $certificate->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Data Umum & Orientasi --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-blue-50 text-[#1b84ff] rounded-lg flex items-center justify-center text-xs font-bold">1</span>
                Informasi Dasar & Orientasi
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Sertifikat (Internal) <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $certificate->title) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Orientasi Sertifikat <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-6 h-[42px] px-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700">
                            <input type="radio" name="orientation" value="portrait" {{ old('orientation', $certificate->orientation) == 'portrait' ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                            <span>Portrait (Berdiri)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700">
                            <input type="radio" name="orientation" value="landscape" {{ old('orientation', $certificate->orientation) == 'landscape' ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                            <span>Landscape (Mendatar)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ old('status', $certificate->status) == 'active' ? 'selected' : '' }}>Aktif (Dapat Diakses User)</option>
                        <option value="draft" {{ old('status', $certificate->status) == 'draft' ? 'selected' : '' }}>Draft (Disembunyikan)</option>
                    </select>
                </div>
            </div>

            {{-- Media Upload Card Group --}}
            <div class="bg-gray-50/70 p-5 rounded-xl border border-gray-200 space-y-4">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    File Gambar & Logo Sertifikat
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Gambar Background</label>
                        <input type="file" name="background_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($certificate->background_image)
                            <p class="text-xs text-emerald-600">File aktif: <a href="{{ asset('storage/' . $certificate->background_image) }}" target="_blank" class="underline font-medium">Lihat File</a></p>
                        @else
                            <p class="text-[11px] text-gray-400">Orientasi file gambar harus sesuai dengan orientasi sertifikat terpilih (Portrait/Landscape).</p>
                        @endif
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Custom Logo Kiri (Kop)</label>
                        <input type="file" name="header_left_logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($certificate->header_left_logo)
                            <p class="text-xs text-emerald-600">File aktif: <a href="{{ asset('storage/' . $certificate->header_left_logo) }}" target="_blank" class="underline font-medium">Lihat File</a></p>
                        @endif
                        <label class="flex items-center gap-2 pt-1 cursor-pointer">
                            <input type="checkbox" name="remove_logo_bg_auto" value="1" checked class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="text-xs text-gray-600 font-medium">Hapus BG putih otomatis</span>
                        </label>
                    </div>

                    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-2xs space-y-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Custom Logo Kanan (Kop)</label>
                        <input type="file" name="header_right_logo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if($certificate->header_right_logo)
                            <p class="text-xs text-emerald-600">File aktif: <a href="{{ asset('storage/' . $certificate->header_right_logo) }}" target="_blank" class="underline font-medium">Lihat File</a></p>
                        @endif
                        <label class="flex items-center gap-2 pt-1 cursor-pointer">
                            <input type="checkbox" name="remove_logo_bg_auto" value="1" checked class="w-4 h-4 text-blue-600 rounded border-gray-300">
                            <span class="text-xs text-gray-600 font-medium">Hapus BG putih otomatis</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Toggle Checkboxes --}}
            <div class="pt-3 border-t border-gray-100 flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_header" value="1" {{ old('show_header', $certificate->show_header) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Tampilkan Kop Surat</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_number" value="1" {{ old('show_number', $certificate->show_number) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Tampilkan Nomor Sertifikat</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_back_page" value="1" {{ old('show_back_page', $certificate->show_back_page) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm font-medium text-gray-700">Tampilkan Halaman Belakang (Struktur Program)</span>
                </label>
            </div>
        </div>

        {{-- Section 2: Recipient Type & Attachment Role --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-blue-50 text-[#1b84ff] rounded-lg flex items-center justify-center text-xs font-bold">2</span>
                Tipe Penerima & Attachment Role Pengguna
            </h2>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Tipe Penerima Sertifikat <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-6 h-[46px] px-4 bg-gray-50 border border-gray-200 rounded-xl">
                    <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700">
                        <input type="radio" name="recipient_type" value="peserta" {{ old('recipient_type', $certificate->recipient_type) == 'peserta' ? 'checked' : '' }} id="type_peserta" class="w-4 h-4 text-blue-600">
                        <span>Sebagai Peserta (Otomatis Sesuai Account User)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700">
                        <input type="radio" name="recipient_type" value="narasumber" {{ old('recipient_type', $certificate->recipient_type) == 'narasumber' ? 'checked' : '' }} id="type_narasumber" class="w-4 h-4 text-blue-600">
                        <span>Sebagai Narasumber (Custom Nama Penerima)</span>
                    </label>
                </div>
            </div>

            {{-- Container untuk Narasumber (Custom Input) --}}
            <div id="narasumber-container" class="space-y-2 {{ old('recipient_type', $certificate->recipient_type) == 'narasumber' ? '' : 'hidden' }}">
                <label class="block text-sm font-semibold text-gray-700">Nama Lengkap & Gelar Narasumber <span class="text-rose-500">*</span></label>
                <input type="text" name="custom_recipient_name" value="{{ old('custom_recipient_name', $certificate->custom_recipient_name) }}" placeholder="Contoh: Dr. H. Ahmad Sudrajat, M.Pd."
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400">Nama ini akan langsung tercetak pada bagian "Diberikan Kepada".</p>
            </div>

            @php
                $attachedRoleIds = $certificate->roles->pluck('id')->toArray();
            @endphp

            {{-- Container untuk Peserta (Checkboxes Role & Select All) --}}
            <div id="peserta-container" class="space-y-4 {{ old('recipient_type', $certificate->recipient_type) == 'peserta' ? '' : 'hidden' }}">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-semibold text-gray-700">Pilih Role Pengguna yang Memiliki Akses Sertifikat Ini <span class="text-rose-500">*</span></label>
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-blue-600 hover:text-blue-800">
                        <input type="checkbox" id="select-all-roles" class="w-4 h-4 text-blue-600 rounded">
                        <span>Pilih Semua Role</span>
                    </label>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-gray-700">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                   {{ in_array($role->id, old('roles', $attachedRoleIds)) ? 'checked' : '' }}
                                   class="role-checkbox w-4 h-4 text-blue-600 rounded">
                            <span>{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Dropdown Pilih User Spesifik (Opsional) --}}
                <div class="pt-3 border-t border-gray-100 space-y-2">
                    <label class="block text-sm font-semibold text-gray-700">Filter / Pilih User Spesifik (Opsional)</label>

                    <div id="user-select-container">
                        @php
                            $attachedUserIds = $certificate->users->pluck('id')->toArray();
                        @endphp
                        <select name="users[]" id="users-select" class="select2-users w-full" multiple="multiple">
                            @foreach($users as $usr)
                                @php
                                    $userRoleIds = $usr->roles->pluck('id')->toArray();
                                    $userRoleNames = implode(', ', $usr->roles->pluck('name')->toArray());
                                @endphp
                                <option value="{{ $usr->id }}"
                                        data-roles="{{ json_encode($userRoleIds) }}"
                                        {{ in_array($usr->id, old('users', $attachedUserIds)) ? 'selected' : '' }}>
                                    {{ $usr->name }} — [{{ $userRoleNames ?: 'Tanpa Role' }}]
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Kosongkan pilihan jika ingin memberikan akses ke semua pengguna yang ada di dalam role yang dipilih di atas.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Konten Sertifikat Halaman Depan --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-blue-50 text-[#1b84ff] rounded-lg flex items-center justify-center text-xs font-bold">3</span>
                Konten Halaman Depan & Tipografi
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Utama</label>
                    <input type="text" name="main_title" value="{{ old('main_title', $certificate->main_title) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Gaya WordArt Judul Utama</label>
                    <select name="word_art_style" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                        <option value="none" {{ old('word_art_style', $certificate->word_art_style) == 'none' ? 'selected' : '' }}>Standar Modern Blue</option>
                        <option value="gold-gradient" {{ old('word_art_style', $certificate->word_art_style) == 'gold-gradient' ? 'selected' : '' }}>WordArt Gold Metallic (Emas Lux)</option>
                        <option value="blue-royal" {{ old('word_art_style', $certificate->word_art_style) == 'blue-royal' ? 'selected' : '' }}>WordArt Royal Gradient (Biru Kerajaan)</option>
                        <option value="emboss-classic" {{ old('word_art_style', $certificate->word_art_style) == 'emboss-classic' ? 'selected' : '' }}>WordArt Emboss 3D Classic</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sub Judul</label>
                    <input type="text" name="sub_title" value="{{ old('sub_title', $certificate->sub_title) }}" required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Format Nomor Sertifikat</label>
                    <input type="text" name="certificate_number_format" value="{{ old('certificate_number_format', $certificate->certificate_number_format) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500 font-mono">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Keterangan Peran / Sub-Caption</label>
                    <input type="text" name="role_caption" value="{{ old('role_caption', $certificate->role_caption) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Teks Narasi Kegiatan / Deskripsi Sertifikat (Rich Text Editor)</label>
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div id="quill-editor" class="h-44 text-sm font-sans">
                        {!! old('content_text', $certificate->content_text) !!}
                    </div>
                </div>
                <input type="hidden" name="content_text" id="content_text_hidden" value="{{ old('content_text', $certificate->content_text) }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tempat & Tanggal Terbit</label>
                    <input type="text" name="place_date" value="{{ old('place_date', $certificate->place_date) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan Penandatangan 1 (Depan)</label>
                    <input type="text" name="signer_1_title" value="{{ old('signer_1_title', $certificate->signer_1_title) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Penandatangan 1 (Employee)</label>
                    <select name="signer_1_employee_id" class="select2-search w-full">
                        <option value="">-- Cari / Pilih Pegawai / Guru --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('signer_1_employee_id', $certificate->signer_1_employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} (NIP: {{ $emp->nip ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 4: Halaman Belakang (Struktur Program) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h2 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center gap-2">
                <span class="w-7 h-7 bg-blue-50 text-[#1b84ff] rounded-lg flex items-center justify-center text-xs font-bold">4</span>
                Halaman Belakang (Struktur Program / Materi)
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Struktur Program</label>
                    <input type="text" name="back_page_title" value="{{ old('back_page_title', $certificate->back_page_title) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jabatan Penandatangan 2 (Belakang)</label>
                    <input type="text" name="signer_2_title" value="{{ old('signer_2_title', $certificate->signer_2_title) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Penandatangan 2 (Employee)</label>
                    <select name="signer_2_employee_id" class="select2-search w-full">
                        <option value="">-- Cari / Pilih Pegawai / Guru --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('signer_2_employee_id', $certificate->signer_2_employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }} (NIP: {{ $emp->nip ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Table Repeater Materi --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Daftar Materi & Alokasi Waktu</label>
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-sm" id="materi-table">
                        <thead class="bg-gray-50 text-gray-700 font-semibold border-b border-gray-200 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 w-12 text-center">No</th>
                                <th class="px-4 py-3">Nama Materi / Modul</th>
                                <th class="px-4 py-3 w-44">Alokasi Waktu</th>
                                <th class="px-4 py-3 w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="materi-container" class="divide-y divide-gray-100">
                            @forelse($certificate->structures as $idx => $struct)
                                <tr class="materi-row">
                                    <td class="px-4 py-3 text-center row-num font-semibold text-gray-500">{{ $idx + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="materi[{{ $idx }}][name]" value="{{ old("materi.{$idx}.name", $struct->materi_name) }}" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="materi[{{ $idx }}][hours]" value="{{ old("materi.{$idx}.hours", $struct->time_allocation) }}" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" class="btn-remove-row text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="materi-row">
                                    <td class="px-4 py-3 text-center row-num font-semibold text-gray-500">1</td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="materi[0][name]" placeholder="Nama Materi Kegiatan" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="materi[0][hours]" placeholder="Contoh: 2 JP" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" class="btn-remove-row text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="button" id="btn-add-materi" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold rounded-xl text-xs transition-colors inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Tambah Baris Materi
                    </button>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.certificates.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-sm transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#1b84ff] hover:bg-[#1570e0] text-white font-semibold rounded-xl text-sm transition-colors shadow-sm shadow-blue-200">
                Perbarui Sertifikat
            </button>
        </div>
    </form>
@endsection

@section('scripts')
    <!-- jQuery, Select2 & Quill JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2-search').select2({
                placeholder: "-- Cari / Pilih Pegawai / Guru --",
                allowClear: true,
                width: '100%'
            });

            function getSelectedRoleIds() {
                return $('.role-checkbox:checked').map(function() {
                    return String($(this).val());
                }).get();
            }

            function matchUserByRole(params, data) {
                const element = data.element;
                if (!element) return null;

                const selectedRoles = getSelectedRoleIds();
                if (selectedRoles.length === 0) return null;

                const userRoles = $(element).data('roles') || [];
                const userRoleStrings = userRoles.map(r => String(r));

                const hasMatchingRole = userRoleStrings.some(roleId => selectedRoles.includes(roleId));
                if (!hasMatchingRole) return null;

                const term = $.trim(params.term || '').toLowerCase();
                if (term === '') return data;

                if (data.text.toLowerCase().indexOf(term) > -1) {
                    return data;
                }

                return null;
            }

            const $usersSelect = $('.select2-users').select2({
                placeholder: "-- Pilih beberapa user spesifik (Opsional) --",
                allowClear: true,
                width: '100%',
                matcher: matchUserByRole
            });

            function syncUserSelectionWithRoles() {
                const selectedRoles = getSelectedRoleIds();

                // Clear unselected role choices
                $('#users-select option:selected').each(function() {
                    const userRoles = $(this).data('roles') || [];
                    const userRoleStrings = userRoles.map(r => String(r));
                    const hasMatchingRole = userRoleStrings.some(roleId => selectedRoles.includes(roleId));
                    if (!hasMatchingRole) {
                        $(this).prop('selected', false);
                    }
                });

                $usersSelect.trigger('change');
            }

            $('.role-checkbox, #select-all-roles').on('change', function() {
                syncUserSelectionWithRoles();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Setup Quill Rich Text Editor
            const Font = Quill.import('formats/font');
            Font.whitelist = ['sans-serif', 'serif', 'monospace', 'great-vibes', 'dancing-script', 'alex-brush', 'sacramento', 'pacifico', 'playfair', 'cinzel', 'tahoma'];
            Quill.register(Font, true);

            const quill = new Quill('#quill-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'font': Font.whitelist }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        ['clean']
                    ]
                }
            });

            // Update hidden input on submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                document.getElementById('content_text_hidden').value = quill.root.innerHTML;
            });

            // Toggle Recipient Type (Peserta vs Narasumber)
            const typePeserta = document.getElementById('type_peserta');
            const typeNarasumber = document.getElementById('type_narasumber');
            const containerPeserta = document.getElementById('peserta-container');
            const containerNarasumber = document.getElementById('narasumber-container');

            function toggleRecipientMode() {
                if (typeNarasumber.checked) {
                    containerNarasumber.classList.remove('hidden');
                    containerPeserta.classList.add('hidden');
                } else {
                    containerPeserta.classList.remove('hidden');
                    containerNarasumber.classList.add('hidden');
                }
            }

            typePeserta.addEventListener('change', toggleRecipientMode);
            typeNarasumber.addEventListener('change', toggleRecipientMode);

            // Select All Roles Handler
            const selectAllRoles = document.getElementById('select-all-roles');
            if (selectAllRoles) {
                selectAllRoles.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.role-checkbox');
                    checkboxes.forEach(cb => cb.checked = selectAllRoles.checked);
                });
            }

            // Table Repeater
            let rowCount = {{ count($certificate->structures) > 0 ? count($certificate->structures) : 1 }};
            const container = document.getElementById('materi-container');
            const btnAdd = document.getElementById('btn-add-materi');

            function updateNumbers() {
                const rows = container.querySelectorAll('.materi-row');
                rows.forEach((row, idx) => {
                    row.querySelector('.row-num').textContent = idx + 1;
                });
            }

            btnAdd.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.className = 'materi-row';
                newRow.innerHTML = `
                    <td class="px-4 py-3 text-center row-num font-semibold text-gray-500">${container.children.length + 1}</td>
                    <td class="px-4 py-3">
                        <input type="text" name="materi[${rowCount}][name]" placeholder="Nama Materi Kegiatan" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white">
                    </td>
                    <td class="px-4 py-3">
                        <input type="text" name="materi[${rowCount}][hours]" placeholder="Contoh: 18 - Jam / 2 JP" class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:bg-white">
                    </td>
                    <td class="px-4 py-3 text-center">
                        <button type="button" class="btn-remove-row text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
                    </td>
                `;
                container.appendChild(newRow);
                rowCount++;
                updateNumbers();
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-row')) {
                    if (container.querySelectorAll('.materi-row').length > 1) {
                        e.target.closest('tr').remove();
                        updateNumbers();
                    }
                }
            });
        });
    </script>
@endsection
