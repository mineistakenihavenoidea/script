<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Siswa;
use App\Models\Staff;
use App\Models\DomainPerkembangan;

class Perkembangan extends Model
{
    protected $table = 'perkembangan';
    use SoftDeletes;

    protected $fillable = [
        'nama_siswa',
        'nama_guru',
        'nilai_motorik_halus',
        'nilai_motorik_kasar',
        'nilai_bahasa',
        'nilai_sosial_kemandirian',
        'kelas',
    ];

    public function classifyScore($score)
    {
        if ($score >= 80) {
            return 'sesuai perkembangan';
        }

        if ($score >= 60) {
            return 'butuh stimulasi';
        }

        return 'butuh perhatian khusus';
    }
    //
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $domains = [
            'motorik_halus',
            'motorik_kasar',
            'bahasa',
            'sosial_kemandirian',
        ];

        foreach ($domains as $domain) {

            $indicators = DomainPerkembangan::where('domain', $domain)->pluck('id');

            $yes = 0;

            foreach ($indicators as $id) {
                if (($data["indikator_$id"] ?? null) === 'yes') {
                    $yes++;
                }
            }

            $total = count($indicators);

            $data["nilai_$domain"] = ($yes / $total) * 100;
        }

        return $data;
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nama_siswa', 'nama_siswa');
    }

    public function guru()
    {
        return $this->belongsTo(Staff::class, 'nama_guru', 'nama_guru');
    }
}
