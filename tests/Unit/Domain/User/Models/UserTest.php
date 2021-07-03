<?php

namespace Tests\Unit\Domain\User\Models;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testUserDatabaseHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'id', 'name', 'email', 'document', 'user_type_id', 'password',
            ]),
            1
        );
    }

    public function testMustReturnInstanceWallet()
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(Wallet::class, $user->wallet);
        $this->assertEquals(1, $user->wallet->id);
        $this->assertEquals(0, $user->wallet->balance);
    }
}
