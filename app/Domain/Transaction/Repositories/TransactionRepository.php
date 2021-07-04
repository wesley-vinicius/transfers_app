<?php

namespace App\Domain\Transaction\Repositories;

use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create(Transaction $transaction): Transaction
    {
        $transaction->save();
        return $transaction->fresh();
    }
}
