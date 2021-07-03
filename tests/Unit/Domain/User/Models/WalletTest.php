<?php

namespace Tests\Unit\Domain\User\Models;

use App\Domain\User\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testWalletDatabaseHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('wallets', [
                'id', 'balance', 'user_id'
            ]),
            1
        );
    }

    public function testThrowExceptionValueIsLessThanZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $wallet = Wallet::factory()->state(['balance' => 0])->make();
        $wallet->deposit(0);
    }

    public function testThrowExceptionValueIsNegative()
    {
        $this->expectException(\InvalidArgumentException::class);
        $wallet = Wallet::factory()->state(['balance' => -1])->make();
        $wallet->deposit(0);
    }

    /**
     * @dataProvider getValuesValidDeposit
     */
    public function testMustChangeBalanceWhenNumberIsGreaterThanZero($value_initial, $value, $value_expected)
    {   
        $wallet = Wallet::factory()->state(['balance' => $value_initial])->make();
        $wallet->deposit($value);

        $this->assertEquals($wallet->balance, $value_expected);
    }

    public function getValuesValidDeposit()
    {
        return [
            'Deposit integer amount' => [
                'value_initial'   => 0,
                'value'   => 100,
                'value_expected' => 100,
            ],
            'Deposit integer amount with balance in wallet' => [
                'value_initial'   => 100,
                'value'   => 100,
                'value_expected' => 200,
            ],
            'Deposit decimal amount' => [
                'value_initial'   => 0,
                'value'   => 100.05,
                'value_expected' => 100.05,
            ],
            'Deposit decimal amount with balance in wallet' => [
                'value_initial'   => 100.25,
                'value'   => 100.66,
                'value_expected' => 200.91,
            ],
        ];
    }
}
