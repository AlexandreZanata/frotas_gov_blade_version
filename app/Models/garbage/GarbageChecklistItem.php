<?php

namespace App\Models\garbage;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarbageChecklistItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description'];
}
