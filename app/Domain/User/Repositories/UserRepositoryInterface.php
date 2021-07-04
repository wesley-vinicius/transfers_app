<?php

namespace App\Domain\User\Repositories;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;

interface UserRepositoryInterface
{
    public function save(User $user): User;
    public function findUserById(int $id): User;
    public function saveWallet(Wallet $wallet): void;
    public function updateWallet(Wallet $wallet): Wallet;
}
