<?php

namespace App\Domain\Transaction\Notifications;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationsTransaction
{
    protected string $url;
    public function __construct()
    {
        $this->url = 'http://o4d9z.mocklab.io/notify';
    }

    public function execute($transaction)
    {
        $valueTranfer = number_format($transaction->value, 2, ',', '.');
        $mensage = "Nova transferência recebida R$: {$valueTranfer}";

        $response = Http::post($this->url, [
            'mensage' => $mensage,
        ]);

        if ($response['message'] === 'Success') {
            Log::info("envio notificação transação #{$transaction->id} realizado.  Mensagem: {$mensage}");
        } else {
            Log::info("Não foi possivel enviar notificacao transação #:{$transaction->id}");
        }
    }
}
