<?php

namespace Database\Factories;

use App\Domain\User\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;


class WalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'balance' => floatval(rand(400, 2000))
        ];
    }
}
