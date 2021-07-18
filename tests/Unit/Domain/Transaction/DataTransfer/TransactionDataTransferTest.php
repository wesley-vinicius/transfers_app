<?php

namespace Tests\Unit\Domain\Transaction\DataTransfer;

use App\Domain\Transaction\DataTransfer\TransactionDataTransfer;
use App\Domain\Transaction\Models\Transaction;
use Tests\TestCase;

class TransactionDataTransferTest extends TestCase
{
    public function testMustReturnTransactionDataTransfer()
    {
        $transaction =  Transaction::factory(['value' => 100])->make();
        $transactionDataTransfer = new TransactionDataTransfer($transaction);

        $this->assertInstanceOf(TransactionDataTransfer::class, $transactionDataTransfer);
        $this->assertEquals($transaction->payer_id, $transactionDataTransfer->payer_id);
        $this->assertEquals($transaction->payee_id, $transactionDataTransfer->payee_id);
        $this->assertEquals($transaction->value, $transactionDataTransfer->value);
    }

    public function testMustReturnResponseDataForUser()
    {
        $transaction =  Transaction::factory(['value' => 100])->make();
        $transactionDataTransfer = new TransactionDataTransfer($transaction);

        $this->assertEquals([
            'id' => 1,
            'payer' => 1,
            'payee' => 2,
            'value' => 100
        ], $transactionDataTransfer->fromResponse());
    }
}
