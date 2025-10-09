<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleTireLayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'layout_data',
    ];

    protected $casts = [
        'layout_data' => 'array',
    ];
}
