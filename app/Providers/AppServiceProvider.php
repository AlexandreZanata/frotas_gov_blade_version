<?php

namespace App\Providers;

use App\Models\garbage\GarbageMaintenanceTareVehicle;
use App\Observers\GarbageMaintenanceTareVehicleObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\fuel\Fueling;
use App\Observers\FuelingObserver;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        GarbageMaintenanceTareVehicle::observe(GarbageMaintenanceTareVehicleObserver::class);
        Fueling::observe(FuelingObserver::class);
    }
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


}
