<?php

namespace Database\Factories;

use App\Domain\Transaction\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => 1,
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => rand(0, 10000)
        ];
    }
}
