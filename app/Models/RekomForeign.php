<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomForeign extends Model
{
    protected $table = 'rekom_foreigns';
    use SoftDeletes;

    protected $fillable = [
        'nama_siswa',
        'nama_rekomendasi',
        'jenis_rekomendasi',
        //
    ];
    //
}
