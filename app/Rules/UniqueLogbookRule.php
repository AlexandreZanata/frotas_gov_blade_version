<?php

namespace App\Rules;

use App\Models\LogbookRule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueLogbookRule implements ValidationRule
{
    protected $existingRuleId;
    protected $targetType;

    public function __construct($existingRuleId = null, $targetType = null)
    {
        $this->existingRuleId = $existingRuleId;
        $this->targetType = $targetType;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetType = $this->targetType ?? request('target_type');

        // Para regras globais, o valor é null
        $targetId = $targetType === 'global' ? null : $value;

        // DEBUG: Log para verificar o que está sendo validado
        \Log::info("Validando regra: target_type={$targetType}, target_id={$targetId}, existing_rule_id={$this->existingRuleId}");

        // VALIDAÇÃO PARA REGRAS GLOBAIS
        if ($targetType === 'global') {
            $query = LogbookRule::where('target_type', 'global')
                ->whereNull('target_id')
                ->where('is_active', true);

            // No caso de update, excluir a própria regra da verificação
            if ($this->existingRuleId) {
                $query->where('id', '!=', $this->existingRuleId);
            }

            $existingRule = $query->first();

            if ($existingRule) {
                \Log::warning("Tentativa de criar segunda regra global. Regra existente: {$existingRule->id}");
                $fail('Já existe uma regra global ativa no sistema. Só é permitida uma regra global por vez.');
                return;
            }
        }
        // VALIDAÇÃO PARA REGRAS ESPECÍFICAS
        else {
            // Só valida se tiver um target_id
            if (!$targetId) {
                return;
            }

            $query = LogbookRule::where('target_type', $targetType)
                ->where('target_id', $targetId)
                ->where('is_active', true);

            if ($this->existingRuleId) {
                $query->where('id', '!=', $this->existingRuleId);
            }

            $existingRule = $query->first();

            if ($existingRule) {
                $targetName = $this->getTargetName($targetType, $targetId);
                $fail("Já existe uma regra ativa para {$targetName}. Cada alvo só pode ter uma regra ativa.");
            }
        }
    }

    /**
     * Obtém o nome do target para a mensagem de erro
     */
    private function getTargetName($targetType, $targetId)
    {
        if (!$targetId) return 'este alvo';

        try {
            switch ($targetType) {
                case 'vehicle_category':
                    $target = \App\Models\VehicleCategory::find($targetId);
                    return $target ? "a categoria '{$target->name}'" : 'esta categoria de veículo';

                case 'user':
                    $target = \App\Models\User::find($targetId);
                    return $target ? "o usuário '{$target->name}'" : 'este usuário';

                case 'vehicle':
                    $target = \App\Models\Vehicle::with('prefix')->find($targetId);
                    if ($target && $target->prefix) {
                        return "o veículo '{$target->prefix->name} - {$target->name}'";
                    }
                    return $target ? "o veículo '{$target->name}'" : 'este veículo';

                default:
                    return 'este alvo';
            }
        } catch (\Exception $e) {
            \Log::error("Erro ao obter nome do target: " . $e->getMessage());
            return 'este alvo';
        }
    }
}
