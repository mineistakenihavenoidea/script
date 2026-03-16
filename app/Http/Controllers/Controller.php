<?php

namespace App\Http\Controllers;

use App\Models\Perkembangan;

abstract class Controller
{
    //

    public function show($id)
    {
        $perkembangan = Perkembangan::find($id);

        $siswa = $perkembangan->siswa->nama_siswa;
        $staff = $perkembangan->staff->nama_guru;

        return view('perkembangan.show', compact('perkembangan','siswa','staff'));
    }
}
