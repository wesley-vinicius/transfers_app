<?php

namespace Tests\Feature\Domain\User\Repository;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;
use App\Domain\User\Repositories\UserRepository;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $userRepository = new UserRepository();
        $returnRepository = $userRepository->save($user);

        $this->assertInstanceOf(User::class, $returnRepository);
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

        $userRepository = new UserRepository();
        $returnRepository = $userRepository->findUserById($user->id);
        $this->assertDatabaseHas('users', [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "document" => $user->document,
            "user_type_id" => $user->user_type_id,
        ]);
        $this->assertEquals($user->toArray(), $returnRepository->toArray());
    }

    public function testMustReturnModelNotFoundExcption()
    {
        $this->expectException(ModelNotFoundException::class);
     
        $userRepository = new UserRepository();
        $userRepository->findUserById(100);
    }

    public function testMustSaveWallet()
    {
        User::unsetEventDispatcher();

        $user = User::factory()->create();
        
        $wallet = new Wallet([
            'user_id' => $user->id,
            'balance' => 100
        ]);

        $userRepository = new UserRepository();
        $userRepository->saveWallet($wallet);

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

        $userRepository = new UserRepository();
        $userRepository->updateWallet($wallet);

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

        $userRepository = new UserRepository();
        $userRepository->updateWallet($wallet);
    }
}
