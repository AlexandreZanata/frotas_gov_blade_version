<?php

namespace App\Models\defect;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefectCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];
}
