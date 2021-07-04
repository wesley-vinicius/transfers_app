<?php

namespace App\Domain\Transaction\Providers;

use App\Domain\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domain\Transaction\Repositories\TransactionRepository;
use App\Domain\Transaction\Services\AuthorizeTransaction;
use App\Domain\Transaction\Services\Contracts\AuthorizeTransactionInterface;
use Carbon\Laravel\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionRepository::class);
        $this->app->bind(AuthorizeTransactionInterface::class, AuthorizeTransaction::class);
    }
}
