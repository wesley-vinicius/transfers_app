<?php

namespace Tests\Feature\Domain\Transaction\Http\Controllers;

use App\Domain\Transaction\Events\SendNotification;
use Illuminate\Support\Facades\Event;
use App\Domain\Transaction\Services\AuthorizeTransaction;
use App\Domain\Transaction\Services\Contracts\AuthorizeTransactionInterface;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        $this->mockAuthorizeTransaction = $this->createMock(AuthorizeTransaction::class);
        parent::setUp();
        $this->artisan('db:seed --class=UserTypesSeeder');
    }

    public function testPayingUserMustBeRegistered()
    {
        $response = $this->postJson(route('transaction'), [
            'payer' => 1,
            'payee' => 2,
            'value' => 100.00
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payer']);
    }

    public function testReceivingUserMustBeRegistered()
    {
        $user = User::factory()->state(['user_type_id' => 1])->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $user->id,
            'payee' => 2,
            'value' => 100.00
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payee']);
    }

    public function testPayingUserCannotARetailer()
    {
        $user = User::factory(2)->state(['user_type_id' => 2])->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $user[0]->id,
            'payee' => $user[1]->id,
            'value' => 10.00
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Retailer cannot transfer',
            'data' => null
        ]);
    }

    public function testTransactionCannotForTheSameUser()
    {
        $user = User::factory()->state(['user_type_id' => 1])->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $user->id,
            'payee' => $user->id,
            'value' => 100.00
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['payee']);
    }

    public function testTransactionValueCannotBeLessThanZero()
    {
        $user = User::factory(2)->state(['user_type_id' => 1])->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $user[0]->id,
            'payee' => $user[1]->id,
            'value' => -1.00
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['value']);
    }

    public function testTransactionValueCannotBeZero()
    {
        $user = User::factory(2)->state(['user_type_id' => 1])->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $user[0]->id,
            'payee' => $user[1]->id,
            'value' => 0
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['value']);
    }

    public function testPayerInsufficientBalance()
    {
        $payer = User::factory()->state(['user_type_id' => 1])
        ->hasWallet(1,['balance' => 100])
        ->create();
        $payee = User::factory()->state(['user_type_id' => 2])
        
        ->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 1000.00
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => 'The user have insufficient balance to withdraw',
            'data' => null
        ]);
    }

    public function testTransactionBetweenCommonUser()
    {
        Event::fake([
            SendNotification::class
        ]);
        
        User::unsetEventDispatcher();
        $this->mockAuthorizeTransaction->method('authorized')->willReturn(true);
        $this->app->instance(AuthorizeTransactionInterface::class, $this->mockAuthorizeTransaction);

        $payer = User::factory()->state(['user_type_id' => 1])
        ->hasWallet(1,['balance' => 1000])
        ->create();
        $payee = User::factory()->state(['user_type_id' => 1])
        ->hasWallet(1,['balance' => 0])
        ->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 500
        ]);

        $response->assertStatus(Response::HTTP_OK)
        ->assertExactJson([ 
            'message' => 'Transaction performed successfully',
            'data' => [
                "id" => 1,
                "payer" => $payer->id,
                "payee" =>  $payee->id,
                "value" => 500.00
            ]
        ]);

    
        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 500
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 500
        ]);

        Event::assertDispatched(SendNotification::class);

    }

    public function testTransactionBetweenOrdinaryUserAndRetail()
    {
        Event::fake([
            SendNotification::class
        ]);
        
        User::unsetEventDispatcher();
        $this->mockAuthorizeTransaction->method('authorized')->willReturn(true);
        $this->app->instance(AuthorizeTransactionInterface::class, $this->mockAuthorizeTransaction);

        $payer = User::factory()->state(['user_type_id' => 1])
        ->hasWallet(1,['balance' => 1000])
        ->create();
        $payee = User::factory()->state(['user_type_id' => 2])
        ->hasWallet(1,['balance' => 0])
        ->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 500.00
        ]);

        $response->assertStatus(Response::HTTP_OK)
        ->assertExactJson([ 
            'message' => 'Transaction performed successfully',
            'data' => [
                "id" => 1,
                "payer" => $payer->id,
                "payee" =>  $payee->id,
                "value" => 500.00
            ]
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 500
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 500
        ]);

        Event::assertDispatched(SendNotification::class);

    }

    public function testUnauthorizedTransaction()
    {
        User::unsetEventDispatcher();
        $this->mockAuthorizeTransaction->method('authorized')->willReturn(false);
        $this->app->instance(AuthorizeTransactionInterface::class, $this->mockAuthorizeTransaction);

        $payer = User::factory()->state(['user_type_id' => 1])
        ->hasWallet(1,['balance' => 1000])
        ->create();
        $payee = User::factory()->state(['user_type_id' => 2])
        ->hasWallet(1,['balance' => 0])
        ->create();

        $response = $this->postJson(route('transaction'), [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 500.00
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Unauthorized transaction',
            'data' => null
        ]);
    }
}
