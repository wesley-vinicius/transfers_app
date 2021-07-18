<?php

namespace Tests\Unit\Domain\Transaction\Services;

use App\Domain\Transaction\DataTransfer\CreateTransactionDataTransfer;
use App\Domain\Transaction\DataTransfer\TransactionDataTransfer;
use Illuminate\Support\Facades\Event;
use App\Domain\Transaction\Events\SendNotification;
use App\Domain\Transaction\Exceptions\RetailerCannotTransferException;
use App\Domain\Transaction\Exceptions\UnauthorizedTransactionException;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Repositories\TransactionRepository;
use App\Domain\Transaction\Services\AuthorizeTransaction;
use App\Domain\Transaction\Services\CreateTransactionService;
use App\Domain\User\Models\User;
use App\Domain\User\Models\Wallet;
use App\Domain\User\Repositories\UserRepository;
use Tests\TestCase;

class CreateTransactionServiceTest extends TestCase
{
    private $transactionRepositoryMock;

    public function setUp(): void
    {
        $this->transactionRepositoryMock = $this->createMock(TransactionRepository::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->authorizeTransactionMock = $this->createMock(AuthorizeTransaction::class);
        parent::setUp();
    }

    public function testMustReturnTransactionDataTransfer()
    {

        Event::fake([
            SendNotification::class
        ]);
        
        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => 1000
        ];

        $createTransactionDataTransfer =  CreateTransactionDataTransfer::fromRequest($data);

        $payer = User::factory(['user_type_id' => 1])
        ->make();
        $payer->wallet = new Wallet(['balance' => 1001]);

        $payee = User::factory(['user_type_id' => 1])
        ->make();
        $payee->wallet = new Wallet(['balance' => 0]);

        $transaction =  Transaction::factory($data)->make();

        $this->transactionRepositoryMock->method('create')
        ->willReturn($transaction);

        $this->userRepositoryMock->method('findUserById')
        ->willReturn($payer, $payee);

        $this->authorizeTransactionMock->method('authorized')
        ->willReturn(true);

        $createUserService = new CreateTransactionService(
            $this->transactionRepositoryMock,
            $this->userRepositoryMock,
            $this->authorizeTransactionMock
        );
        
        $returnService = $createUserService->execute($createTransactionDataTransfer);

        $this->assertInstanceOf(TransactionDataTransfer::class, $returnService);
        $this->assertEquals($createTransactionDataTransfer->value, $returnService->value);

        Event::assertDispatched(SendNotification::class);
    }

    public function testMustReturnExceptionRetailerCannotTransfer()
    {
        $this->expectException(RetailerCannotTransferException::class);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => 1000
        ];

        $createTransactionDataTransfer =  CreateTransactionDataTransfer::fromRequest($data);

        $transaction = new Transaction($data);
        $user = User::factory(['user_type_id' => 2])->make();

        $this->transactionRepositoryMock->method('create')
        ->willReturn($transaction);

        $this->userRepositoryMock->method('findUserById')
        ->willReturn($user);

        $this->authorizeTransactionMock->method('authorized')
        ->willReturn(true);

        $createUserService = new CreateTransactionService(
            $this->transactionRepositoryMock,
            $this->userRepositoryMock,
            $this->authorizeTransactionMock
        );
        
        $createUserService->execute($createTransactionDataTransfer);
    }

    public function testMustReturnExceptionUnauthorizedTransaction()
    {
        $this->expectException(UnauthorizedTransactionException::class);

        $data = [
            'payer_id' => 1,
            'payee_id' => 2,
            'value' => 1000
        ];

        $createTransactionDataTransfer =  CreateTransactionDataTransfer::fromRequest($data);

        $transaction = new Transaction($data);
        $user = User::factory(['user_type_id' => 1])->make();

        $this->transactionRepositoryMock->method('create')
        ->willReturn($transaction);

        $this->userRepositoryMock->method('findUserById')
        ->willReturn($user);

        $this->authorizeTransactionMock->method('authorized')
        ->willReturn(false);

        $createUserService = new CreateTransactionService(
            $this->transactionRepositoryMock,
            $this->userRepositoryMock,
            $this->authorizeTransactionMock
        );
        
        $createUserService->execute($createTransactionDataTransfer);
    }
}
