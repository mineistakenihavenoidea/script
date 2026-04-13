<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class Staff extends Authenticatable implements FilamentUser
{
    use notifiable;

    use SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'nama_guru',
        'jabatan',
        'wali_kelas',
        'foto',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
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
