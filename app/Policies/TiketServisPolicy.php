<?php

namespace App\Policies;

use App\Models\TiketServis;
use App\Models\User;

class TiketServisPolicy
{
    public function view(User $user, TiketServis $tiketServis): bool
    {
        return $user->hasAnyRole(['admin', 'teknisi'])
            || $user->id === $tiketServis->user_id;
    }

    public function update(User $user, TiketServis $tiketServis): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('teknisi')) {
            return $tiketServis->teknisi_id === null || $tiketServis->teknisi_id === $user->id;
        }

        return $user->id === $tiketServis->user_id && $tiketServis->status === 'menunggu';
    }

    public function delete(User $user, TiketServis $tiketServis): bool
    {
        return $user->hasRole('admin')
            || ($user->id === $tiketServis->user_id && $tiketServis->status === 'menunggu');
    }
}