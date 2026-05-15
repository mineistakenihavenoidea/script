<?php

use Illuminate\Support\Facades\Route;
use App\Models\Perkembangan;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DomainPerkembangan;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/perkembangan/{id}/pdf', function ($id) {
    $perkembangan = Perkembangan::findOrFail($id);

    $indikatorDefinisinya = DomainPerkembangan::where('kelompok_usia', $perkembangan->kelompok_usia)
        ->orderBy('domain', 'asc')
        ->get()
        ->groupBy(function ($item) {
            return ucwords(strtolower(trim($item->domain)));
        });
        
    $pdf = Pdf::loadView('pdf.perkembangan', [
        'record' => $perkembangan,
        'indikatorDefinisinya' => $indikatorDefinisinya
    ]);

    return $pdf->stream("perkembangan_{$perkembangan->nama_siswa}.pdf");
})
->name('perkembangan.print');

require __DIR__.'/settings.php';
