<?php

namespace Tests\Unit\Domain\Transaction\Http\Controllers;

use App\Domain\Transaction\Exceptions\RetailerCannotTransferException;
use App\Domain\Transaction\Exceptions\UnauthorizedTransactionException;
use App\Domain\Transaction\Http\Controllers\TransactionController;
use App\Domain\Transaction\Http\Requests\TransactionRequest;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Services\CreateTransactionService;
use App\Domain\User\Exceptions\InsuficienteBalanceException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testMustReturnStatusCodeOk()
    {
        $data =  [
            'payer' => 1,
            'payee' => 2,
            'value' => 1000
        ];

        $transaction = new Transaction($data);

        $transactionServiceMock = $this->mock(CreateTransactionService::class);
        $transactionServiceMock->shouldReceive('execute')
            ->once()
            ->andReturn($transaction);

        $transactionRequest = TransactionRequest::create('/transaction', 'POST', $data);

        /** @var CreateTransactionService $transactionServiceMock */
        $createTransactionController = new TransactionController($transactionServiceMock);
        $returnTransactionController = $createTransactionController->execute($transactionRequest);

        $this->assertInstanceOf(JsonResponse::class, $returnTransactionController);
        $this->assertEquals(Response::HTTP_OK, $returnTransactionController->getStatusCode());
    }

    public function testMustReturnServerErrorWhenThrowsException()
    {
        $e = new \Exception();
        $transactionServiceMock = $this->createMock(CreateTransactionService::class);
        $transactionServiceMock->expects(self::exactly(1))
            ->method('execute')
            ->willThrowException($e);

        $data =  [
            'payer' => 1,
            'payee' => 2,
            'value' => 1000
        ];

        $transactionRequest = TransactionRequest::create('/transaction', 'POST', $data);

        /** @var CreateTransactionService $transactionServiceMock */
        $createTransactionController = new TransactionController($transactionServiceMock);
        $returnTransactionController = $createTransactionController->execute($transactionRequest);

        $this->assertInstanceOf(JsonResponse::class, $returnTransactionController);
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $returnTransactionController->getStatusCode());
    }


    /**
     *
     * @dataProvider getErrorsWithException
     */
    public function testErrorsWithException($exception, $status_code)
    {
        $transactionServiceMock = $this->createMock(CreateTransactionService::class);
        $transactionServiceMock->expects(self::exactly(1))
            ->method('execute')
            ->willThrowException($exception);

        $data =  [
            'payer' => 1,
            'payee' => 2,
            'value' => 1000
        ];

        $transactionRequest = TransactionRequest::create('/transaction', 'POST', $data);

        /** @var CreateTransactionService $transactionServiceMock */
        $createTransactionController = new TransactionController($transactionServiceMock);
        $returnTransactionController = $createTransactionController->execute($transactionRequest);

        $this->assertInstanceOf(JsonResponse::class, $returnTransactionController);
        $this->assertEquals($status_code, $returnTransactionController->getStatusCode());
    }

    public function getErrorsWithException()
    {
        return [
            'Transaction Unauthorized' => [new UnauthorizedTransactionException(), 'status_code' => Response::HTTP_UNAUTHORIZED],
            'Retailer cannot transfer' => [new RetailerCannotTransferException(), 'status_code' => Response::HTTP_UNAUTHORIZED],
            'User without balence sufficient' => [new InsuficienteBalanceException(), 'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY],
        ];
    }
}
