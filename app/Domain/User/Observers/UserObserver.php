<?php

namespace App\Domain\User\Observers;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;
use App\Domain\User\Repositories\UserRepositoryInterface;

class UserObserver
{

    private UserRepositoryInterface $userRepository;

    /**
     * Class constructor.
     */

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $wallet = new Wallet([
            'user_id' => $user->id,
            'balance' => 0,
        ]);
        
        $this->userRepository->createWallet($wallet);
    }
}
