<?php

namespace App\Models\fuel;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelingViewLog extends Model // Ou FuelingViewedBy
{
    use HasFactory, HasUuids;

    /**
     * O nome da tabela associada ao model.
     * É AQUI QUE VOCÊ DEFINE O NOME CORRETO DA TABELA.
     *
     * @var string
     */
    protected $table = 'fueling_view_logs';

    /**
     * Indica se o modelo deve ter timestamps (created_at, updated_at).
     * Definimos como false pois já temos `viewed_at`.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', // Adicione o 'id' se você o preencheu manualmente no controller
        'fueling_id',
        'user_id',
        'viewed_at', // Remova se estiver usando useCurrent() na migration
        'ip_address',
        'user_agent',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Define a relação inversa: Um log de visualização pertence a um Abastecimento.
     */
    public function fueling(): BelongsTo
    {
        // Ajuste o namespace do Model Fueling se necessário
        return $this->belongsTo(\App\Models\fuel\Fueling::class, 'fueling_id');
    }

    /**
     * Define a relação inversa: Um log de visualização pertence a um Usuário.
     */
    public function user(): BelongsTo
    {
        // Assume que o model User está em App\Models\User ou App\Models\user\User
        return $this->belongsTo(\App\Models\user\User::class, 'user_id'); // Ajuste o namespace se necessário
    }
}
