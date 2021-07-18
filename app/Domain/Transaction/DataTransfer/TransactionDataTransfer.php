<?php

namespace App\Domain\Transaction\DataTransfer;

use App\Core\DataTransfers\DataTransfer;
use App\Domain\Transaction\Models\Transaction;

class TransactionDataTransfer extends DataTransfer
{
    protected int $id;
    protected int $payer_id;
    protected int $payee_id;
    protected float $value;

    public function __construct(Transaction $transaction)
    {
        $this->id = $transaction->id;
        $this->payer_id = $transaction->payer_id;
        $this->payee_id = $transaction->payee_id;
        $this->value = $transaction->value;
    }

    public function fromResponse()
    {
        return [
            'id' => $this->id,
            'payer' => $this->payer_id,
            'payee' => $this->payee_id,
            'value' => $this->value
        ];
    }
}
