<?php

namespace App\Domain\Transaction\Services;

use App\Domain\Transaction\DataTransfer\CreateTransactionDataTransfer;
use App\Domain\Transaction\DataTransfer\TransactionDataTransfer;
use App\Domain\Transaction\Events\SendNotification;
use App\Domain\Transaction\Exceptions\RetailerCannotTransferException;
use App\Domain\Transaction\Exceptions\UnauthorizedTransactionException;
use App\Domain\Transaction\Models\Transaction;
use App\Domain\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domain\Transaction\Services\Contracts\AuthorizeTransactionInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CreateTransactionService
{
    private AuthorizeTransactionInterface $authorizeTransaction;
    private TransactionRepositoryInterface $transactionRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        UserRepositoryInterface $userRepository,
        AuthorizeTransactionInterface $authorizeTransaction
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->authorizeTransaction = $authorizeTransaction;
    }

    public function execute(CreateTransactionDataTransfer $createTransactionDataTransfer): TransactionDataTransfer
    {  
        $transaction = DB::transaction(function () use ($createTransactionDataTransfer) {

            $payer = $this->userRepository->findUserById($createTransactionDataTransfer->payer_id);
            $payee = $this->userRepository->findUserById($createTransactionDataTransfer->payee_id);

            if ($payer->isRetailer()) {
                throw new RetailerCannotTransferException(
                    'Retailer cannot transfer'
                );
            }
            if (! $this->authorizeTransaction->authorized()) {
                throw new UnauthorizedTransactionException(
                    'Unauthorized transaction'
                );
            }

            $transaction = new Transaction([
                'payer_id' => $createTransactionDataTransfer->payer_id,
                'payee_id' => $createTransactionDataTransfer->payee_id,
                'value' => $createTransactionDataTransfer->value,
            ]);

            $payer->wallet->withdraw($createTransactionDataTransfer->value);
            $payee->wallet->deposit($createTransactionDataTransfer->value);
    
            $this->userRepository->updateWallet($payer->wallet);
            $this->userRepository->updateWallet($payee->wallet);

            return $this->transactionRepository->create($transaction);

        });

        event(new SendNotification($transaction));
        return new TransactionDataTransfer($transaction);
    }
}
