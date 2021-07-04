<?php

namespace Tests\Feature\Domain\User\Repository;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Repositories\TransactionRepository;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testMustSaveTransactionAndReturnTransaction()
    {
        $payer = User::factory(['user_type_id' => 1])->create();
        $payee = User::factory()->create();

        $transactionRepository = new TransactionRepository();

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 1000
        ];

        $transaction = new Transaction($data);
        $returnRepository = $transactionRepository->create($transaction);

        $this->assertInstanceOf(Transaction::class, $returnRepository);
        $this->assertDatabaseHas('transactions', [
            "id" => 1,
            "value" => 1000,
            "payer_id" => $payer->id,
            "payee_id" => $payee->id,
        ]);
    }
}
