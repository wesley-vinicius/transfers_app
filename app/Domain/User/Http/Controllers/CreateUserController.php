<?php

namespace App\Domain\User\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Domain\User\Services\CreateUserService;
use App\Domain\User\Http\Requests\UserCreateRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CreateUserController extends Controller
{

    private CreateUserService $createUserService;

    public function __construct(CreateUserService $createUserService)
    {
        $this->createUserService = $createUserService;
    }

    public function execute(UserCreateRequest $request)
    {
        try {
            $user = $this->createUserService->execute($request->all());
            return $this->success(
                ["user" => ['id' => $user->id]],
                'user created successfully',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::critical($e);
            return $this->error('could not create user :(', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
