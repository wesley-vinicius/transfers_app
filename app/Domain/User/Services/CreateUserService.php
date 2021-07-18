<?php

namespace App\Domain\User\Services;

use App\Domain\User\DataTransfer\CreateUserDataTransfer;
use App\Domain\User\DataTransfer\UserDataTransfer;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(CreateUserDataTransfer $createUserDataTransfer): UserDataTransfer
    {
        $user = DB::transaction(function () use ($createUserDataTransfer) {
            $user = new User([
                'name' => $createUserDataTransfer->name,
                'email' => $createUserDataTransfer->email,
                'document' => $createUserDataTransfer->document,
                'user_type_id' => $createUserDataTransfer->user_type_id,
                'password' => Hash::make($createUserDataTransfer->password),
            ]);
    
            return $this->userRepository->save($user);
        });

        return new UserDataTransfer($user);
    }
}
