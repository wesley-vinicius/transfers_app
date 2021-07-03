<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;

interface UserRepositoryInterface
{
    public function save(User $user): User;

    public function createWallet(Wallet $wallet): void;
}
