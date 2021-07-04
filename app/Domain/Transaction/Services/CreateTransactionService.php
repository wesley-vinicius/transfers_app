<?php

namespace App\Domain\Transaction\Services;

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

    public function execute(array $data)
    {
        $payer = $this->userRepository->findUserById($data['payer_id']);
        $payee = $this->userRepository->findUserById($data['payee_id']);

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
            'payer_id' => $data['payer_id'],
            'payee_id' => $data['payee_id'],
            'value' => $data['value'],
        ]);

        $payer->wallet->withdraw($data['value']);
        $payee->wallet->deposit($data['value']);

        $transaction = DB::transaction(function () use ($transaction, $payer, $payee) { 
            $this->userRepository->updateWallet($payer->wallet);
            $this->userRepository->updateWallet( $payee->wallet);

            return $this->transactionRepository->create($transaction);
        });
        event(new SendNotification($transaction));

        return $transaction;
    }
}
