<?php

namespace App\Domain\Transaction\Repositories\Contracts;

use App\Domain\Transaction\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(Transaction $transaction);
}
