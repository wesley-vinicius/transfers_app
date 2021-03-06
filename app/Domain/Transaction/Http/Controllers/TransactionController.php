<?php

namespace App\Domain\Transaction\Http\Controllers;

use App\Core\Http\Controllers\Controller;
use App\Domain\Transaction\DataTransfer\CreateTransactionDataTransfer;
use App\Domain\Transaction\Exceptions\RetailerCannotTransferException;
use App\Domain\Transaction\Exceptions\UnauthorizedTransactionException;
use App\Domain\Transaction\Http\Requests\TransactionRequest;
use App\Domain\Transaction\Services\CreateTransactionService;
use App\Domain\User\Exceptions\InsuficientBalanceException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    private CreateTransactionService $createTransactionService;

    public function __construct(CreateTransactionService $createTransactionService)
    {
        $this->createTransactionService = $createTransactionService;
    }

    public function execute(TransactionRequest $request)
    {
        try {
            $createTransactionDataTransfer = CreateTransactionDataTransfer::fromRequest($request->fromCreateTransaction());
            $transactionDataTranfer = $this->createTransactionService->execute($createTransactionDataTransfer);

            return $this->success(
                $transactionDataTranfer->fromResponse(),
                'Transaction performed successfully'
            );
            
        } catch (UnauthorizedTransactionException | RetailerCannotTransferException $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        } catch (InsuficientBalanceException $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $exception) {
            Log::critical($exception);
            return $this->error('It was not possible to perform the transaction', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
