<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';
    use SoftDeletes;

    protected $fillable = [
        'nama_rekomendasi',
        'jenis_rekomendasi',
    ];
    //
}
