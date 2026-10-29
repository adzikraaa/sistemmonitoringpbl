<?php

namespace App\Providers;

use App\Models\Dosen;
use App\Models\Koordinator;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'mahasiswa'  => Mahasiswa::class,
            'dosen'      => Dosen::class,
            'koordinator' => Koordinator::class,
        ]);
    }
}
