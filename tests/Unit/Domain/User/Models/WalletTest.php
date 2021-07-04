<?php

namespace Tests\Unit\Domain\User\Models;

use App\Domain\User\Exceptions\InsuficientBalanceException;
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
        $wallet = Wallet::factory()->state(['balance' => 0])->make();
        $wallet->deposit(-1);
    }

    /**
     * @dataProvider getValuesValidDeposit
     */
    public function testMustChangeBalanceWhenNumberIsGreaterThanZero($balance_initial, $value, $balance_expected)
    {
        $wallet = Wallet::factory()->state(['balance' => $balance_initial])->make();
        $wallet->deposit($value);

        $this->assertEquals($wallet->balance, $balance_expected);
    }

    public function getValuesValidDeposit()
    {
        return [
            'Deposit integer amount' => [
                'balance_initial'   => 0,
                'value'   => 100,
                'balance_expected' => 100,
            ],
            'Deposit integer amount with balance in wallet' => [
                'balance_initial'   => 100,
                'value'   => 100,
                'balance_expected' => 200,
            ],
            'Deposit decimal amount' => [
                'balance_initial'   => 0,
                'value'   => 100.05,
                'balance_expected' => 100.05,
            ],
            'Deposit decimal amount with balance in wallet' => [
                'balance_initial'   => 100.25,
                'value'   => 100.66,
                'balance_expected' => 200.91,
            ],
        ];
    }

    public function testThrowExceptionIfAmountWithdrawalLessThanZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $wallet = Wallet::factory()->state(['balance' => 0])->make();
        $wallet->withdraw(-1);
    }

    public function testThrowExceptionIfAmountWithdrawalEqualsZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $wallet = Wallet::factory()->state(['balance' => 0])->make();
        $wallet->withdraw(0);
    }

    public function testThrowExceptionIfBalanceInsuficiente()
    {
        $this->expectException(InsuficientBalanceException::class);
        $wallet = Wallet::factory()->state(['balance' => 100])->make();
        $wallet->withdraw(101);
    }

    /**
     * @dataProvider getValuesValidWithdrawl
     */
    public function testMustChangeBalanceWhenNumberIsGreaterThanZeroWithdrawl($balance_initial, $value, $balance_expected)
    {
        $wallet = Wallet::factory()->state(['balance' => $balance_initial])->make();
        $wallet->withdraw($value);

        $this->assertEquals($wallet->balance, $balance_expected);
    }

    public function getValuesValidWithdrawl()
    {
        return [
            'Deposit integer amount' => [
                'balance_initial'   => 2000,
                'value'   => 100,
                'balance_expected' => 1900,
            ],
            'Deposit decimal amount' => [
                'balance_initial'   => 2000.97,
                'value'   => 100.05,
                'balance_expected' => 1900.92,
            ],
        ];
    }
}
