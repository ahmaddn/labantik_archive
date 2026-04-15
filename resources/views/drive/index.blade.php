@extends('layouts.app')
@section('title', 'My Drive')

@section('content')

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        @php
            $quotaMb = isset($quotaLimit) ? round($quotaLimit / 1024 / 1024, 2) : null; // MB
            $remainingMb = isset($remainingBytes) ? round($remainingBytes / 1024 / 1024, 2) : 0; // MB
        @endphp
        <h1 class="text-2xl font-bold text-gray-900">My Drive</h1>
        <p class="text-gray-500 text-sm mt-1">File yang Anda upload disimpan di Google Drive.</p>
        @if(isset($quotaLimit))
            <p class="text-gray-500 text-sm mt-1">Sisa kuota: {{ $remainingMb }} MB dari {{ $quotaMb }} MB</p>
        @endif
    </div>
    @if(!$isConnected)
        <div class="flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-700">
            ⚠️ Google Drive belum terhubung
        </div>
    @endif
</div>

{{-- Upload Card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
    <h2 class="text-base font-semibold text-gray-800 mb-4">Upload File Baru</h2>

    <form method="POST" action="{{ route('drive.upload') }}" enctype="multipart/form-data"
          id="uploadForm">
        @csrf
        <div class="border-2 border-dashed border-gray-200 hover:border-blue-400 rounded-xl p-8
                    text-center transition-colors cursor-pointer" id="dropZone"
             onclick="document.getElementById('fileInput').click()">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="text-gray-500 text-sm" id="dropText">
                Klik atau drag & drop file di sini
            </p>
                 <p class="text-gray-400 text-xs mt-1">Maksimum per-file: 1 MB. Sisa kuota: {{ $remainingMb }} MB</p>
                 <input type="file" id="fileInput" name="file" accept=".pdf,application/pdf" class="hidden"
                     onchange="showFileName(this)">
                     <p id="clientError" class="text-red-600 text-sm mt-2 hidden"></p>
        </div>

        @error('file')
            <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
        @enderror

        <button type="submit" id="uploadBtn"
                class="mt-4 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold
                       rounded-xl transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            data-server-disabled="{{ (!$isConnected || (isset($remainingBytes) && $remainingBytes <= 0)) ? '1' : '0' }}"
            @if(!$isConnected || (isset($remainingBytes) && $remainingBytes <= 0)) disabled @endif>
            <span id="uploadBtnText">Upload ke Drive</span>
        </button>
    </form>
</div>

{{-- File Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-800">File Saya</h2>
        <span class="text-sm text-gray-500">{{ $files->total() }} file</span>
    </div>

    @if($files->isEmpty())
        <div class="text-center py-16 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm">Belum ada file. Upload file pertama Anda!</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="text-left px-6 py-3">Nama File</th>
                        <th class="text-left px-6 py-3 hidden md:table-cell">Ukuran</th>
                        <th class="text-left px-6 py-3 hidden sm:table-cell">Tanggal Upload</th>
                        <th class="text-center px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($files as $file)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    @if(str_contains($file->mime_type, 'image'))
                                        🖼️
                                    @elseif(str_contains($file->mime_type, 'pdf'))
                                        📄
                                    @elseif(str_contains($file->mime_type, 'video'))
                                        🎬
                                    @elseif(str_contains($file->mime_type, 'audio'))
                                        🎵
                                    @elseif(str_contains($file->mime_type, 'zip') || str_contains($file->mime_type, 'rar'))
                                        📦
                                    @else
                                        📁
                                    @endif
                                </div>
                                <span class="font-medium text-gray-800 truncate max-w-xs">
                                    {{ $file->name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden md:table-cell">
                            {{ $file->formatted_size }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 hidden sm:table-cell">
                            {{ $file->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Download/View --}}
                                <a href="{{ $file->web_view_link }}" target="_blank"
                                   class="flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100
                                          text-blue-700 rounded-lg transition-colors text-xs font-medium">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Download
                                </a>

                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('drive.destroy', $file->id) }}"
                                      onsubmit="return confirm('Hapus file \'{{ addslashes($file->name) }}\'?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100
                                                   text-red-700 rounded-lg transition-colors text-xs font-medium">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($files->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $files->links() }}
            </div>
        @endif
    @endif
</div>

<script>
function showFileName(input) {
    if (input.files[0]) {
        document.getElementById('dropText').textContent = '✅ ' + input.files[0].name;
    }
}

// Upload loading state
document.getElementById('uploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('uploadBtn');
    btn.disabled = true;
    document.getElementById('uploadBtnText').textContent = 'Mengupload...';
});

// Client-side validation: type PDF, max 1MB, and check remaining quota
(function() {
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const clientError = document.getElementById('clientError');
    const serverDisabled = uploadBtn.dataset.serverDisabled === '1';
    const remainingBytes = Number({{ $remainingBytes ?? 0 }});
    const perFileLimit = 1 * 1024 * 1024; // 1 MB in bytes

    function setError(message) {
        clientError.textContent = message;
        clientError.classList.remove('hidden');
    }

    function clearError() {
        clientError.textContent = '';
        clientError.classList.add('hidden');
    }

    fileInput.addEventListener('change', function() {
        clearError();
        const f = fileInput.files && fileInput.files[0];
        if (!f) {
            uploadBtn.disabled = true;
            return;
        }

        if (serverDisabled) {
            setError('Upload dinonaktifkan: cek koneksi Google Drive atau kuota Anda.');
            uploadBtn.disabled = true;
            return;
        }

        // Type check
        const isPdf = f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf');
        if (!isPdf) {
            setError('Hanya file PDF yang diperbolehkan.');
            uploadBtn.disabled = true;
            return;
        }

        // Size per-file check
        if (f.size > perFileLimit) {
            setError('Ukuran file tidak boleh lebih dari 1 MB.');
            uploadBtn.disabled = true;
            return;
        }

        // Remaining quota check
        if (f.size > remainingBytes) {
            const remMb = (remainingBytes / (1024*1024)).toFixed(2);
            setError('Sisa kuota tidak cukup. Sisa: ' + remMb + ' MB.');
            uploadBtn.disabled = true;
            return;
        }

        // All good
        clearError();
        uploadBtn.disabled = false;
    });

    // Initial state: disable upload if server marked disabled or no file selected
    uploadBtn.disabled = serverDisabled || !(fileInput.files && fileInput.files.length);
})();
</script>

@endsection
