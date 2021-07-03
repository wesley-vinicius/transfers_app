<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;

class UserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        $user->save();
        return $user->fresh();
    }

    public function createWallet(Wallet $wallet): void
    {
        $wallet->save();
    }
}
