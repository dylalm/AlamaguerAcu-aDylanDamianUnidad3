<?php

namespace App\Policies;

use App\Models\Herramienta;
use App\Models\User;

class HerramientaPolicy
{
    /** Solo el dueño de una herramienta puede modificarla. */
    public function update(User $user, Herramienta $herramienta): bool
    {
        return $user->id === $herramienta->user_id;
    }
}
