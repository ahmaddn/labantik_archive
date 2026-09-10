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

        $certificates = GoogleCertificate::where('status', 'active')
            ->where(function ($query) use ($user, $roleIds, $roleCodes) {
                if ($user->isSuperAdmin()) {
                    $query->whereRaw('1 = 1');
                } else {
                    $query->where('recipient_type', 'narasumber')
                          ->orWhere(function ($qPeserta) use ($user, $roleIds, $roleCodes) {
                              $qPeserta->where('recipient_type', 'peserta')
                                       ->where(function ($qTarget) use ($user, $roleIds, $roleCodes) {
                                           $qTarget->whereHas('users', function ($qUser) use ($user) {
                                               $qUser->where('core_users.id', $user->id);
                                           })
                                           ->orWhere(function ($qAllRoleUsers) use ($roleIds, $roleCodes) {
                                               $qAllRoleUsers->whereDoesntHave('users')
                                                             ->whereHas('roles', function ($qRole) use ($roleIds, $roleCodes) {
                                                                 $qRole->whereIn('core_roles.id', $roleIds)
                                                                       ->orWhereIn('core_roles.code', $roleCodes);
                                                             });
                                           });
                                       });
                          });
                }
            })
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

        $certificate = GoogleCertificate::where('status', 'active')
            ->where(function ($query) use ($user, $roleIds, $roleCodes) {
                if ($user->isSuperAdmin()) {
                    $query->whereRaw('1 = 1');
                } else {
                    $query->where('recipient_type', 'narasumber')
                          ->orWhere(function ($qPeserta) use ($user, $roleIds, $roleCodes) {
                              $qPeserta->where('recipient_type', 'peserta')
                                       ->where(function ($qTarget) use ($user, $roleIds, $roleCodes) {
                                           $qTarget->whereHas('users', function ($qUser) use ($user) {
                                               $qUser->where('core_users.id', $user->id);
                                           })
                                           ->orWhere(function ($qAllRoleUsers) use ($roleIds, $roleCodes) {
                                               $qAllRoleUsers->whereDoesntHave('users')
                                                             ->whereHas('roles', function ($qRole) use ($roleIds, $roleCodes) {
                                                                 $qRole->whereIn('core_roles.id', $roleIds)
                                                                       ->orWhereIn('core_roles.code', $roleCodes);
                                                             });
                                           });
                                       });
                          });
                }
            })
            ->with(['signer1Employee', 'signer2Employee', 'structures'])
            ->findOrFail($id);

        return view('certificates.template', compact('certificate', 'user'));
    }
}
