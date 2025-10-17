<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleStatus extends Model
{
    use HasFactory, HasUuids;

    /**
     * Retorna as classes de CSS para estilizar o status com uma paleta de cores profissional.
     *
     * @return string
     */
    public function getStyles(): string
    {
        // Usar mb_strtolower para garantir o tratamento correto de caracteres acentuados (UTF-8).
        $statusName = mb_strtolower($this->name, 'UTF-8');

        return match ($statusName) {
            // Ciano (Teal) para status positivos.
            'disponível' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-500 dark:text-white',

            // Amarelo para status de atenção.
            'em manutenção' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500 dark:text-white',

            // Rosa (Rose) para status críticos.
            'em ocorrência', 'avariado', 'bloqueado' => 'bg-rose-100 text-rose-800 dark:bg-rose-500 dark:text-white',

            // Cinza Ardósia (Slate) para status neutros.
            'inativo', 'vendido', 'baixado' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-100',

            // Índigo (Padrão).
            default => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500 dark:text-white',
        };
    }
}
