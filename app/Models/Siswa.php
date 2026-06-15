<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    protected $table = 'siswa';
    use SoftDeletes;

    protected $fillable = [
        'id_siswa',
        'nama_siswa',
        'no_induk',
        'kelas',
        'nama_guru',
        'tanggal_lahir',
        'foto',
        'ta_masuk',
    ];
    //
    public function perkembangan()
    {
        return $this->hasMany(Perkembangan::class, 'nama_siswa', 'nama_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Staff::class, 'nama_guru', 'nama_guru');
    }
}
