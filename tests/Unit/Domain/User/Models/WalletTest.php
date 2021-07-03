<?php

namespace Tests\Unit\Domain\User\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testWalletDatabaseHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('wallets', [
                'id', 'balance', 'user_id'
            ]),
            1
        );
    }
}
