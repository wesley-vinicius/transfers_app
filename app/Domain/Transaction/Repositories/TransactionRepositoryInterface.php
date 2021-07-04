<?php

namespace App\Domain\Transaction\Repositories;

use App\Domain\Transaction\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(Transaction $transaction);
}
