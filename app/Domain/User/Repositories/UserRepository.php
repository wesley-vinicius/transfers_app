<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;
use DomainException;

class UserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        $user->save();
        return $user->fresh();
    }

    public function findUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function saveWallet(Wallet $wallet): void
    {
        $wallet->save();
    }

    public function UpdateWallet(Wallet $wallet): Wallet
    {
        if (is_null($wallet->id)) {
            throw new DomainException('wallet does not exist');
        }

        $wallet->save();
        return $wallet->fresh();
    }
}
