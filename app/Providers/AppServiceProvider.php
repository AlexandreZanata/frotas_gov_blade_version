<?php

namespace App\Providers;

use App\Models\garbage\GarbageMaintenanceTareVehicle;
use App\Observers\GarbageMaintenanceTareVehicleObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Adicione esta linha dentro do método boot()
        GarbageMaintenanceTareVehicle::observe(GarbageMaintenanceTareVehicleObserver::class);
    }
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


}
