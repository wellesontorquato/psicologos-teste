<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Paciente;

class PacientePolicy
{
    /**
     * Determina se o usuário pode visualizar a lista de pacientes.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos os usuários autenticados podem visualizar seus próprios pacientes
    }

    /**
     * Determina se o usuário pode visualizar este paciente.
     */
    public function view(User $user, Paciente $paciente): bool
    {
        return $user->id === $paciente->user_id;
    }

    /**
     * Determina se o usuário pode atualizar este paciente.
     */
    public function update(User $user, Paciente $paciente): bool
    {
        return $user->id === $paciente->user_id;
    }

    /**
     * Determina se o usuário pode deletar este paciente.
     */
    public function delete(User $user, Paciente $paciente): bool
    {
        return $user->id === $paciente->user_id;
    }

    /**
     * Determina se o usuário pode exportar o histórico deste paciente.
     */
    public function exportarHistorico(User $user, Paciente $paciente): bool
    {
        return $user->id === $paciente->user_id;
    }
}
