<?php

namespace Tests\Unit\Domain\User\DataTransfer;

use App\Domain\User\DataTransfer\CreateUserDataTransfer;
use Tests\TestCase;

class CreateUserDataTransferTest extends TestCase
{
    public function testMustReturnUserDataTransfer()
    {
        $faker = \Faker\Factory::create('pt_BR');

        $data = [
            'name' => $faker->name(),
            'email' => $faker->unique()->safeEmail(),
            'user_type_id' => 1,
            'document' => $faker->cpf,
            'password' => 'password',
        ];

        $createUserDataTransfer =  CreateUserDataTransfer::fromRequest($data);

        $this->assertInstanceOf(CreateUserDataTransfer::class, $createUserDataTransfer);
        $this->assertEquals($data['name'], $createUserDataTransfer->name);
        $this->assertEquals($data['email'], $createUserDataTransfer->email);
        $this->assertEquals($data['user_type_id'], $createUserDataTransfer->user_type_id);
        $this->assertEquals($data['document'], $createUserDataTransfer->document);
        $this->assertEquals($data['password'], $createUserDataTransfer->password);
    }
}
