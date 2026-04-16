<?php

namespace App\Policies;

use App\Models\Siswa;
use App\Models\Staff;
use Illuminate\Auth\Access\Response;

class SiswaPolicy
{
    /**
     * Siapa yang bisa melihat halaman daftar data?
     */
    public function viewAny(Staff $user): bool
    {
        // Contoh: Boleh diakses oleh super_admin dan guru
        return in_array($user->jabatan, ['Kepala', 'Staff', 'Guru', 'Guru Pendamping']);
    }

    public function view(Staff $user, Siswa $siswa): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Siapa yang bisa membuat data baru?
     */
    public function create(Staff $user): bool
    {
        // Contoh: Hanya super_admin
        return in_array($user->jabatan, ['Guru', 'Guru Pendamping', 'Staff']);
    }

    /**
     * Siapa yang bisa mengedit data?
     */
    public function update(Staff $user, Siswa $siswa): bool
    {
        return in_array($user->jabatan, ['Guru', 'Guru Pendamping', 'Staff']);
    }

    /**
     * Siapa yang bisa menghapus data?
     */
    public function delete(Staff $user, Siswa $siswa): bool
    {
        return in_array($user->jabatan, ['Guru', 'Guru Pendamping', 'Staff']);
    }
}