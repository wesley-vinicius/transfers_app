<?php

namespace Tests\Unit\Domain\User\Services;

use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Services\CreateUserService;
use Tests\TestCase;

class CreateUserServiceTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMustReturnUser()
    {
        $faker = \Faker\Factory::create('pt_BR');
        $data = [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'user_type_id' => 1,
            'document' =>  $faker->cpf,
            "password"  => "password",
        ];

        $user = User::factory($data)->make();

        $userRepositoryMock = $this->mock(UserRepository::class);
        $userRepositoryMock->shouldReceive('save')
            ->once()
            ->andReturn($user);

        /** @var UserRepositoryInterface $userRepositoryMock */
        $createUserService = new CreateUserService($userRepositoryMock);

        $this->assertEquals($user, $createUserService->execute($data));
    }
}
