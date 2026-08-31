@php
$user = filament()->auth()->user();
@endphp
<x-filament-widgets::widget class="fi-account-widget">
<x-filament::section>      
    {{-- KONTINER UTAMA: Pakai inline CSS agar w-full dan justify-between mutlak jalan --}}
    <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
        {{-- SISI KIRI: Avatar + Text --}}
        <div style="display: flex; align-items: center; gap: 1rem;">              
            {{-- Avatar --}}
            <div style="width: 64px; height: 64px; min-width: 64px; min-height: 64px; overflow: hidden; border-radius: 50%;"
                class="bg-gray-100 dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700">
                    @if ($user->foto)
                        @php
                            // Ambil alamat fisik file langsung dari server/komputer
                            $pathFoto = Storage::disk('lokal')->path($user->foto);
                            
                            $base64Foto = null;
                            if (file_exists($pathFoto)) {
                                // Convert gambar jadi kode Base64 agar langsung dirender browser
                                $tipe = pathinfo($pathFoto, PATHINFO_EXTENSION);
                                $data = file_get_contents($pathFoto);
                                $base64Foto = 'data:image/' . $tipe . ';base64,' . base64_encode($data);
                            }
                        @endphp

                        @if($base64Foto)
                            <img src="{{ $base64Foto }}"
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: bold;"
                                class="text-gray-400 bg-gray-100">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: bold;"
                        class="text-gray-400">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>
            {{-- Info Teks --}}
            <div style="display: flex; flex-direction: column;">
                <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white" style="margin: 0;">
                    Selamat Datang, {{ filament()->getUserName($user) }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5" style="margin: 0.25rem 0 0 0;">
                    <span class="font-medium text-primary-600 dark:text-primary-400">{{ $user->jabatan ?? $user->role }}</span>
                </p>
            </div>              
        </div>
        {{-- SISI KANAN: Logout --}}
        <div>
            <form action="{{ filament()->getLogoutUrl() }}" method="post">
                @csrf
                <x-filament::button
                    color="gray"
                    icon="heroicon-m-arrow-left-on-rectangle"
                    tag="button"
                    type="submit"
                >
                    Logout
                </x-filament::button>
            </form>
        </div>          
    </div>      
</x-filament::section>
</x-filament-widgets::widget>
