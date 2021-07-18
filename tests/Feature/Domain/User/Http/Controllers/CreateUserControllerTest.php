<?php

namespace Tests\Feature\Domain\User\Http\Controllers;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateUserControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->faker = \Faker\Factory::create('pt_BR');
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testRegisterUser()
    {
        $userType = random_int(1, 2);
        $document = $userType == 1 ? $this->faker->cpf : $this->faker->cnpj;

        $password = 'password';
        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'user_type_id' => $userType,
            'document' => $document,
            'password' => $password,
            "password_confirmation" => $password,
        ];

        $response = $this->postJson(
            route('user.create'),
            $payload
        );


        $response->assertExactJson([ 
            'message' => 'user created successfully',
            'data' => [
                'id' => 1,
                'name' => $payload['name'],
                'email' => $payload['email'],
                'user_type_id' => $payload['user_type_id'],
                'document' => $payload['document'],
            ]
        ]);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => 1,
            'email' => $payload['email'],
        ]);

        $this->assertDatabaseCount('wallets', 1);
        $this->assertDatabaseHas('wallets', [
            'id' => 1,
            'balance' => 0
        ]);
    }

    public function testRegistrationEmailAlreadyInUse()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $user->email,
            'user_type_id' => 1,
            'document' => $this->faker->cpf,
            'password' => 'password',
            "password_confirmation" => 'password',
        ];

        $response = $this->postJson(
            route('user.create'),
            $payload
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['message' => true]);
        $response->assertJsonValidationErrors(['email']);
        $response->assertJson([
            "errors" => [
                "email" => ["The email has already been taken."],
            ]
        ]);
    }

    public function testRegistrationDocumentAlreadyInUse()
    {
        $user = User::factory()->create();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'user_type_id' => 1,
            'document' => $user->document,
            'password' => 'password',
            "password_confirmation" => 'password',
        ];

        $response = $this->postJson(
            route('user.create'),
            $payload
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['message' => true]);
        $response->assertJsonValidationErrors(['document']);
        $response->assertJson([
            "errors" => [
                "document" => ["The document has already been taken."],
            ]
        ]);
    }
}
