<?php

namespace Tests\Unit\Domain\Transaction\Services\Services;

use App\Domain\Transaction\Services\AuthorizeTransaction;
use Tests\TestCase;

class AuthorizeTransactionTest extends TestCase
{
    public function testMustReturnTrue()
    {
        $authorizeTransaction = new AuthorizeTransaction();
        $this->assertTrue($authorizeTransaction->authorized());
    }
}
