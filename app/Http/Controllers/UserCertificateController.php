<?php

namespace App\Http\Controllers;

use App\Models\GoogleCertificate;
use Illuminate\Http\Request;

class UserCertificateController extends Controller
{
    /**
     * Tampilkan riwayat sertifikat yang di-attach ke role user login
     */
    public function index()
    {
        $user = auth()->user();
        $roleIds   = $user->roles->pluck('id')->toArray();
        $roleCodes = $user->roles->pluck('code')->toArray();

        // Jika user adalah super admin, atau memiliki role yang cocok (berdasarkan ID atau code)
        $certificates = GoogleCertificate::where(function ($query) use ($user, $roleIds, $roleCodes) {
            if ($user->isSuperAdmin()) {
                $query->whereRaw('1 = 1');
            } else {
                $query->whereHas('roles', function ($q) use ($roleIds, $roleCodes) {
                    $q->whereIn('core_roles.id', $roleIds)
                      ->orWhereIn('core_roles.code', $roleCodes);
                });
            }
        })
        ->where('status', 'active')
        ->with(['signer1Employee', 'signer2Employee', 'structures'])
        ->latest()
        ->paginate(10);

        return view('user.certificates.index', compact('certificates', 'user'));
    }

    /**
     * Tampilan / Cetak sertifikat spesifik milik user
     */
    public function show($id)
    {
        $user = auth()->user();
        $roleIds   = $user->roles->pluck('id')->toArray();
        $roleCodes = $user->roles->pluck('code')->toArray();

        $certificate = GoogleCertificate::where(function ($query) use ($user, $roleIds, $roleCodes) {
            if ($user->isSuperAdmin()) {
                $query->whereRaw('1 = 1');
            } else {
                $query->whereHas('roles', function ($q) use ($roleIds, $roleCodes) {
                    $q->whereIn('core_roles.id', $roleIds)
                      ->orWhereIn('core_roles.code', $roleCodes);
                });
            }
        })
        ->where('status', 'active')
        ->with(['signer1Employee', 'signer2Employee', 'structures'])
        ->findOrFail($id);

        return view('certificates.template', compact('certificate', 'user'));
    }
}
