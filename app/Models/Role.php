<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // Importe a trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory, HasUuids; // Use a trait
}
