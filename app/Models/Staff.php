<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    protected $table = 'staff';
    use SoftDeletes;

    protected $fillable = [
        'nama_guru',
        'jabatan',
        'wali_kelas',
        'is_admin',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'is_admin' => 'boolean',
        ];
    }
<<<<<<< HEAD

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // for Laravel / general usage
    public function getNameAttribute(): string
    {
        return $this->nama_guru ?? $this->username ?? 'User';
    }

    // for Filament (future-proof)
    public function getFilamentName(): string
    {
        return $this->nama_guru ?? $this->username ?? 'User';
    }

    // role checkers
    public function isKepala(): bool
    {
        return $this->jabatan === 'Kepala';
    }

    public function isGuru(): bool
    {
        return $this->jabatan === 'Guru';
    }

    public function isGuruPendamping(): bool
    {
        return $this->jabatan === 'Guru Pendamping';
    }

    public function isStaff(): bool
    {
        return $this->jabatan === 'Staff';
    }

    // permissions
    public function canCrudPerkembangan(): bool
    {
        return $this->isGuru() || $this->isGuruPendamping() || $this->isStaff();
    }

    public function canReadPerkembangan(): bool
    {
        return $this->isKepala();
    }

    public function canCrudStaff(): bool
    {
        return $this->isStaff();
    }

    public function canReadStaff(): bool
    {
        return $this->isKepala();
    }

    public function canCrudSiswa(): bool
    {
        return $this->isGuru() || $this->isGuruPendamping() || $this->isStaff();
    }

    public function canReadSiswa(): bool
    {
        return $this->isKepala();
    }

    public function canCrudDomainPerkembangan(): bool
    {
        return $this->isStaff();
    }

    public function canReadDomainPerkembangan(): bool
    {
        return $this->isKepala();
    }

    public function canCrudRekomendasi(): bool
    {
        return $this->isStaff();
    }

    public function canReadRekomendasi(): bool
    {
        return $this->isKepala();
    }
=======
>>>>>>> parent of 7ba824e (trying to fix login)
    //

    public function perkembangan()
    {
        return $this->hasMany(Perkembangan::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
