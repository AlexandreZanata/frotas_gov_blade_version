<?php

namespace App\Models\Vehicle;

use App\Models\defect\DefectReport;
use App\Models\fines\Fine;
use App\Models\fuel\Fueling;
use App\Models\maintenance\OilChange;
use App\Models\maintenance\ServiceOrder;
use App\Models\maintenance\Tire;
use App\Models\run\Run;
use App\Models\user\Secretariat;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory, HasUuids, Auditable;

    protected $fillable = [
        'prefix_id',
        'name',
        'brand_id',
        'model_year',
        'plate',
        'chassis',
        'renavam',
        'registration',
        'fuel_tank_capacity',
        'fuel_type_id',
        'category_id',
        'status_id',
        'secretariat_id',
        'heritage_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function prefix(): BelongsTo
    {
        return $this->belongsTo(Prefix::class, 'prefix_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(VehicleStatus::class, 'status_id');
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\fuel\FuelType::class, 'fuel_type_id');
    }

    public function serviceOrders()
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function fuelings()
    {
        return $this->hasMany(Fueling::class);
    }

    public function runs()
    {
        return $this->hasMany(Run::class);
    }

    public function fines()
    {
        return $this->hasMany(Fine::class);
    }

    public function defectReports()
    {
        return $this->hasMany(DefectReport::class);
    }

    public function transfers()
    {
        return $this->hasMany(VehicleTransfer::class);
    }

    public function oilChanges()
    {
        return $this->hasMany(OilChange::class);
    }

    public function tires()
    {
        return $this->hasMany(Tire::class, 'current_vehicle_id');
    }

    /**
     * Relação com a secretaria
     */
    public function secretariat()
    {
        return $this->belongsTo(Secretariat::class);
    }

    /**
     * Busca a última corrida completada
     */
    public function lastCompletedRun()
    {
        return $this->hasOne(Run::class)->where('status', 'completed')->latest('finished_at');
    }

    /**
     * Busca corrida em andamento
     */
    public function activeRun()
    {
        return $this->hasOne(Run::class)->where('status', 'in_progress');
    }

    public function heritage(): BelongsTo
    {
        return $this->belongsTo(VehicleHeritage::class);
    }

    public function priceOrigin(): HasOne
    {
        return $this->hasOne(VehiclePriceOrigin::class);
    }

    public function priceCurrent(): HasOne
    {
        return $this->hasOne(VehiclePriceCurrent::class);
    }

    public function priceHistories(): HasMany
    {
        return $this->hasMany(VehiclePriceHistory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class);
    }
}
