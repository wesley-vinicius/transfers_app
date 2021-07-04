<?php

namespace Tests\Feature\Domain\User\Repository;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;
use App\Domain\User\Repositories\UserRepository;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testMustSaveUserAndReturnToUser()
    {
        $user = User::factory()->make();
        $userRespository = new UserRepository();
        $returnRespository = $userRespository->save($user);

        $this->assertInstanceOf(User::class, $returnRespository);
        $this->assertDatabaseHas('users', [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "document" => $user->document,
            "user_type_id" => $user->user_type_id,
        ]);
        $this->assertDatabaseHas('wallets', [
           'balance' => 0
        ]);
    }

    public function testMustReturnUserById()
    {
        $user = User::factory()->create();
        $user->fresh();

        $userRespository = new UserRepository();
        $returnRespository = $userRespository->findUserById($user->id);
        $this->assertDatabaseHas('users', [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "document" => $user->document,
            "user_type_id" => $user->user_type_id,
        ]);
        $this->assertEquals($user->toArray(), $returnRespository->toArray());
    }

    public function testMustSaveWallet()
    {
        User::unsetEventDispatcher();

        $user = User::factory()->create();
        
        $wallet = new Wallet([
            'user_id' => $user->id,
            'balance' => 100
        ]);

        $userRespository = new UserRepository();
        $userRespository->saveWallet($wallet);

        $this->assertDatabaseHas('wallets', [
            "id" => 1,
            "user_id" => $user->id,
            "balance" => 100,
        ]);
    }

    public function testMustUpdateWallet()
    {
        $user = User::factory()->create();
        
        $this->assertDatabaseHas('wallets', [
            "id" => 1,
            "user_id" => $user->id,
            "balance" => 0,
        ]);

        $wallet = $user->wallet;
        $wallet->deposit(55);

        $userRespository = new UserRepository();
        $userRespository->UpdateWallet($wallet);

        $this->assertDatabaseHas('wallets', [
            "id" => 1,
            "user_id" => $user->id,
            "balance" => 55,
        ]);
    }

    public function testMustReturnExcptionWalletNotExist()
    {
        $this->expectException(DomainException::class);

        User::unsetEventDispatcher();
        $user = User::factory()->create();
        
        $wallet = new Wallet([
            'user_id' => $user->id,
            'balance' => 100
        ]);

        $userRespository = new UserRepository();
        $userRespository->UpdateWallet($wallet);
    }
}
