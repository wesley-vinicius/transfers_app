<?php

namespace App\Domain\User\Services;

use App\Domain\User\DataTransfer\UserDataTransfer;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CreateUserService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(array $data): UserDataTransfer
    {
        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'document' => $data['document'],
            'user_type_id' => $data['user_type_id'],
            'password' => Hash::make($data['password']),
        ]);

        $user = $this->userRepository->save($user);

        return new UserDataTransfer($user);
    }
}
