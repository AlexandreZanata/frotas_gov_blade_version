<?php

namespace App\Models\user;

use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secretariat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'acronym',
        'manager_name',
        'manager_contact',
        'email',
        'phone',
    ];

    /**
     * Relação com usuários
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relação com veículos
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
