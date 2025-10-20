<?php

namespace App\Models\run;

use App\Models\run\Run;
use App\Models\user\User;
use App\Models\Vehicle\Vehicle;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RunGapFind extends Model
{
    use HasFactory, HasUuids;

    /**
     * O nome da tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'run_gap_finds'; // Especifica o nome da tabela (plural)

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'run_id',
        'vehicle_id',
        'user_id',
        'recorded_start_km',
        'expected_start_km',
        'gap_km',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorded_start_km' => 'integer',
            'expected_start_km' => 'integer',
            'gap_km' => 'integer',
        ];
    }

    /**
     * Relacionamento: O registro de gap pertence a uma Corrida (Run).
     */
    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class, 'run_id');
    }

    /**
     * Relacionamento: O registro de gap pertence a um Veículo (Vehicle).
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    /**
     * Relacionamento: O registro de gap foi criado por um Usuário (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
