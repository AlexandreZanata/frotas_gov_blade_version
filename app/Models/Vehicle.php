<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'prefix_id',
        'name',
        'brand',
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
    ];
}
