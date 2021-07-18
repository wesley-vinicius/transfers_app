<?php

namespace Tests\Unit\Domain\Transaction\DataTransfer;

use App\Domain\Transaction\DataTransfer\CreateTransactionDataTransfer;
use Tests\TestCase;

class CreateTransactionDataTransferTest extends TestCase
{
    public function testMustReturnCreateTransactionDataTransfer()
    {
        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => 1000
        ];

        $createTransactionDataTransfer =  CreateTransactionDataTransfer::fromRequest($data);

        $this->assertInstanceOf(CreateTransactionDataTransfer::class, $createTransactionDataTransfer);
        $this->assertEquals($data['payer_id'], $createTransactionDataTransfer->payer_id);
        $this->assertEquals($data['payee_id'], $createTransactionDataTransfer->payee_id);
        $this->assertEquals($data['value'], $createTransactionDataTransfer->value);
    }
}
