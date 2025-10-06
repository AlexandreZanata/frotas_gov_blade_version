<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Checklist extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['run_id', 'user_id', 'notes'];

    public function run(): BelongsTo { return $this->belongsTo(Run::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function answers(): HasMany { return $this->hasMany(ChecklistAnswer::class); }
}
