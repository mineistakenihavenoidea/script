<?php

namespace App\Policies;

use App\Models\Perkembangan;
use App\Models\Staff; // Gunakan model User jika sistem login kamu memakai tabel users
use Illuminate\Auth\Access\Response;

class PerkembanganPolicy
{
    /**
     * Siapa yang bisa melihat halaman daftar data?
     */
    public function viewAny(Staff $user): bool
    {
        // Contoh: Boleh diakses oleh super_admin dan guru
        return in_array($user->jabatan, ['Kepala', 'Guru', 'Guru Pendamping']);
    }

    public function view(Staff $user, Perkembangan $perkembangan): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Siapa yang bisa membuat data baru?
     */
    public function create(Staff $user): bool
    {
        return in_array($user->jabatan, ['Guru Pendamping', 'Guru']);
    }

    /**
     * Siapa yang bisa mengedit data?
     */
    public function update(Staff $user, Perkembangan $perkembangan): bool
    {
        return in_array($user->jabatan, ['Guru Pendamping', 'Guru']);
    }

    /**
     * Siapa yang bisa menghapus data?
     */
    public function delete(Staff $user, Perkembangan $perkembangan): bool
    {
        return in_array($user->jabatan, ['Guru Pendamping', 'Guru']);
    }
}
