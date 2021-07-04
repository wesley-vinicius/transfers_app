<?php
namespace App\Domain\Transaction\Services\Contracts;

interface AuthorizeTransactionInterface
{
    public function authorized(): bool;
}
