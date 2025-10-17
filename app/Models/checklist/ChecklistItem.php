<?php
namespace App\Models\checklist;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['name', 'description'];
}
