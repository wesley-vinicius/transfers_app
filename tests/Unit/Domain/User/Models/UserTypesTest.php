<?php

namespace Tests\Unit\Domain\User\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTypesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testWalletDatabaseHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('user_types', [
                'id', 'name'
            ]),
            1
        );
    }
}
