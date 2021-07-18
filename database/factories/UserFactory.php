<?php

namespace Database\Factories;

use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;


class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('pt_BR');
        $userType = random_int(1, 2);
        $document = $userType == 1 ? $faker->cpf : $faker->cnpj;

        return [
            'id' => rand(1,1000),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'user_type_id' => $userType,
            'document' => $document,
            'password' => Hash::make('password')
        ];
    }
}
