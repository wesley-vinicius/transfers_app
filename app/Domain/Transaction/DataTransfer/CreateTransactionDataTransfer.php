<?php
namespace App\Domain\Transaction\DataTransfer;

use App\Core\DataTransfers\DataTransfer;

class CreateTransactionDataTransfer extends DataTransfer
{
    protected int $payer_id;
    protected int $payee_id;
    protected float $value;

    public function __construct(int $payer_id, int $payee_id, float $value)
    {
        $this->payer_id = $payer_id;
        $this->payee_id = $payee_id;
        $this->value = $value;
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['payer_id'],
            $data['payee_id'],
            $data['value']
        );
    }
}
