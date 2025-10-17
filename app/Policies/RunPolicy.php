<?php

namespace App\Policies;

use App\Models\run\Run;
use App\Models\user\User;

class RunPolicy
{
    /**
     * Determine if the user can view any runs.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos podem ver suas próprias corridas
    }

    /**
     * Determine if the user can view the run.
     */
    public function view(User $user, Run $run): bool
    {
        // Usuário pode ver sua própria corrida
        // Gestores podem ver corridas de veículos da sua secretaria
        return $user->id === $run->user_id
            || ($user->isManager() && $user->secretariat_id === $run->vehicle->secretariat_id);
    }

    /**
     * Determine if the user can create runs.
     */
    public function create(User $user): bool
    {
        return true; // Todos os usuários autenticados podem criar corridas
    }

    /**
     * Determine if the user can update the run.
     */
    public function update(User $user, Run $run): bool
    {
        // Usuário só pode atualizar sua própria corrida
        // E apenas se estiver em andamento
        return $user->id === $run->user_id && $run->status === 'in_progress';
    }

    /**
     * Determine if the user can delete the run.
     */
    public function delete(User $user, Run $run): bool
    {
        // Usuário pode cancelar sua própria corrida em andamento
        // Gestores podem cancelar corridas de veículos da sua secretaria
        return ($user->id === $run->user_id && $run->status === 'in_progress')
            || ($user->isManager() && $user->secretariat_id === $run->vehicle->secretariat_id);
    }

    /**
     * Determine if the user can restore the run.
     */
    public function restore(User $user, Run $run): bool
    {
        return $user->isGeneralManager();
    }

    /**
     * Determine if the user can permanently delete the run.
     */
    public function forceDelete(User $user, Run $run): bool
    {
        return $user->isGeneralManager();
    }
}

