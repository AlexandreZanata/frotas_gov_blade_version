<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GarbageMaintenanceTareVehicle;
use App\Observers\GarbageMaintenanceTareVehicleObserver;

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
