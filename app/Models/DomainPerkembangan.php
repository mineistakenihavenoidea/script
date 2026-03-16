<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DomainPerkembangan extends Model
{
    protected $table = 'domain_perkembangans';
    use softdeletes;

    protected $fillable = [
        'domain',
        'kelompok_usia',
        'indikator',
    ];
    //
}
