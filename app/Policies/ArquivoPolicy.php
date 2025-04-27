<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Arquivo;

class ArquivoPolicy
{
    /**
     * Verifica se o usuário pode visualizar o arquivo.
     */
    public function view(User $user, Arquivo $arquivo): bool
    {
        return $user->id === $arquivo->paciente->user_id;
    }

    /**
     * Verifica se o usuário pode deletar o arquivo.
     */
    public function delete(User $user, Arquivo $arquivo): bool
    {
        return $user->id === $arquivo->paciente->user_id;
    }

    public function update(User $user, Arquivo $arquivo): bool
    {
        return $user->id === $arquivo->paciente->user_id;
    }
}
