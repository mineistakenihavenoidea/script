<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Perkembangan;
use App\Policies\PerkembanganPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    protected $policies = [
        Perkembangan::class => PerkembanganPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
