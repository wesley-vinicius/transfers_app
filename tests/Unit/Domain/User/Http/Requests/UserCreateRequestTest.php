<?php

namespace Tests\Unit\Domain\User\Http\Requests;

use App\Domain\User\Http\Requests\UserCreateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreateRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testReturnArrayWithValidationsToCreateUser()
    {
        $rulesUserCreate = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'document' => ['required', 'string', 'max:20', 'unique:users'],
            'user_type_id' => ['required', 'integer', 'exists:user_types,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $userCreateRequest = new UserCreateRequest();

        $this->assertEquals($rulesUserCreate, $userCreateRequest->rules());
    }

    public function testAuthorizeTrue()
    {
        $userCreateRequest = new UserCreateRequest();

        $this->assertTrue($userCreateRequest->authorize());
    }
}
