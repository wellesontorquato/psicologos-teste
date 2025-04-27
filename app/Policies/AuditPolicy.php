<?php

namespace App\Policies;

use App\Models\User;

class AuditPolicy
{
    public function viewAudit(User $user): bool
    {
        return $user->is_admin === true;
    }
}

