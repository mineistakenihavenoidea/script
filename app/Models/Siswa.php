<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    protected $table = 'siswa';
    use SoftDeletes;

    protected $fillable = [
        'nama_siswa',
        'no_induk',
        'kelas',
        'nama_guru',
        'tanggal_lahir',
        'foto',
    ];
    //
    public function perkembangan()
    {
        return $this->hasMany(Perkembangan::class);
    }

    public function guru()
    {
        return $this->belongsTo(Staff::class, 'nama_guru', 'nama_guru');
    }
}
