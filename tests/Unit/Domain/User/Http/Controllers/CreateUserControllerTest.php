<?php

namespace Tests\Unit\Domain\User\Http\Controllers;

use App\Domain\User\DataTransfer\UserDataTransfer;
use App\Domain\User\Http\Controllers\CreateUserController;
use App\Domain\User\Http\Requests\UserCreateRequest;
use App\Domain\User\Models\User;
use App\Domain\User\Services\CreateUserService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateUserControllerTest extends TestCase
{

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->faker = \Faker\Factory::create('pt_BR');
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMustReturnUser()
    {
        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'user_type_id' => 1,
            'document' => $this->faker->cpf,
            'password' => 'password',
            "password_confirmation" => 'password',
        ];

        $user = User::factory($data)->make();
        $userDataTransfer = new UserDataTransfer($user);

        $userServiceMock = $this->mock(CreateUserService::class);
        $userServiceMock->shouldReceive('execute')
            ->once()
            ->andReturn($userDataTransfer);

        $this->faker = \Faker\Factory::create('pt_BR');

        $userCreateRequest = UserCreateRequest::create('/user', 'POST', $data);
        
        /** @var CreateUserService $userServiceMock */
        $createUserController = new CreateUserController($userServiceMock);
        $returnUserController = $createUserController->execute($userCreateRequest);

        $this->assertInstanceOf(JsonResponse::class, $returnUserController);
        $this->assertEquals(Response::HTTP_CREATED ,$returnUserController->getStatusCode());
    }

    public function testMustReturnStatusCodeInternalServerErrorWhenThrowsException()
    {
        $e = new \Exception();
        $userServiceMock = $this->createMock(CreateUserService::class);
        $userServiceMock->expects(self::exactly(1))
            ->method('execute')
            ->willThrowException($e);


        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'user_type_id' => 1,
            'document' => $this->faker->cpf,
            'password' => 'password',
            "password_confirmation" => 'password',
        ];

        $userCreateRequest = UserCreateRequest::create('/user', 'POST', $data);
        
        /** @var CreateUserService $userServiceMock */
        $createUserController = new CreateUserController($userServiceMock);
        $returnUserController = $createUserController->execute($userCreateRequest);

        $this->assertInstanceOf(JsonResponse::class, $returnUserController);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR ,$returnUserController->getStatusCode());
    }
}
