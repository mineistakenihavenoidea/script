<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\DomainPerkembangan;
use Illuminate\Auth\Access\Response;

class DomainPerkembanganPolicy
{
    /**
     * Siapa yang bisa melihat halaman daftar data?
     */
    public function viewAny(Staff $user): bool
    {
        // Allowed jabatans: Kepala, Staff, Guru, Guru Pendamping can view; only Staff can create, update, or delete.
        return in_array($user->jabatan, ['Kepala', 'Staff', 'Guru', 'Guru Pendamping']);
    }

    public function view(Staff $user, DomainPerkembangan $domainPerkembangan): bool
    {
        return $this->viewAny($user);
    }
    /**
     * Siapa yang bisa membuat data baru?
     */
    public function create(Staff $user): bool
    {
        // Contoh: Hanya Staff
        // Only Staff can create new data
        return $user->jabatan === 'Staff';
    }

    /**
     * Siapa yang bisa mengedit data?
     */
    public function update(Staff $user, DomainPerkembangan $domainPerkembangan): bool
    {
        return $user->jabatan === 'Staff';
    }

    /**
     * Siapa yang bisa menghapus data?
     */
    public function delete(Staff $user, DomainPerkembangan $domainPerkembangan): bool
    {
        return $user->jabatan === 'Staff';
    }
}
