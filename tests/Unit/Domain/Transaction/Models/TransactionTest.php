<?php

namespace Tests\Unit\Domain\Transaction\Models;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testTransactionDatabaseHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('transactions', [
                'id', 'payer_id', 'payee_id', 'value',
            ]),
            1
        );
    }

    public function testThrowExceptionValueIsLessThanZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Transaction([
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => 0
        ]);
    }

    public function testThrowExceptionValueIsNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Transaction([
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => -1
        ]);
    }

    public function testMustReturnInstanceUserPayer()
    {
        $user = User::factory()->create();

        $transaction = new Transaction([
            'payer_id' => $user->id,
            'payee_id' => 2,
            'value' => 1000
        ]);

        $payer = $transaction->payer;

        $this->assertInstanceOf(User::class, $payer);
        $this->assertEquals($user->toArray(), $payer->toArray());
    }

    public function testMustReturnInstanceUserPayee()
    {
        $user = User::factory()->create();

        $transaction = new Transaction([
            'payer_id' => 3,
            'payee_id' => $user->id,
            'value' => 1000
        ]);

        $payee = $transaction->payee;

        $this->assertInstanceOf(User::class, $payee);
        $this->assertEquals($user->toArray(), $payee->toArray());
    }
}
