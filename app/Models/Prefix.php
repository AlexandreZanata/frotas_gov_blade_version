<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefix extends Model {
    use HasFactory, HasUuids;
    protected $fillable = ['name'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'prefix_id');
    }
}
