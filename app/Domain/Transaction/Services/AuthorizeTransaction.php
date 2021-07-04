<?php

namespace App\Domain\Transaction\Services;

use App\Domain\Transaction\Services\Contracts\AuthorizeTransactionInterface;
use Illuminate\Support\Facades\Http;

class AuthorizeTransaction implements AuthorizeTransactionInterface
{
    private string $url;

    public function __construct()
    {
        $this->url = 'https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';
    }

    public function authorized(): bool
    {
        $response = Http::get($this->url);
        $dataRetorno = $response->json();

        return $dataRetorno['message'] === 'Autorizado';
    }
}
