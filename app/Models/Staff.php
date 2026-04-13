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

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
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
