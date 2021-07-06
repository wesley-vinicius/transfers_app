<?php

namespace Tests\Unit\Domain\Transaction\Http\Requests;

use App\Domain\Transaction\Http\Requests\TransactionRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testReturnArrayWithValidationsToCreateTransaction()
    {
        $rulesUserCreate = [
            "payer" => ['bail', 'required', 'integer', 'exists:users,id'],
            "payee" => ['bail', 'required', 'integer', 'different:payer', 'exists:users,id'],
            "value" => ['required', 'gt:0', 'numeric']
        ];

        $userCreateRequest = new TransactionRequest();

        $this->assertEquals($rulesUserCreate, $userCreateRequest->rules());
    }

    public function testAuthorizeTrue()
    {
        $userCreateRequest = new TransactionRequest();

        $this->assertTrue($userCreateRequest->authorize());
    }

    public function testMustReturnArrayToCreateTransaction()
    {
        $data =  [
            'payer' => 1,
            'payee' => 2,
            'value' => 1000
        ];

        $transactionRequest = TransactionRequest::create('/transaction', 'POST', $data);

        $this->assertEquals([
            'payer_id' => $data['payer'],
            'payee_id' => $data['payee'],
            'value' => $data['value']
        ], $transactionRequest->fromCreateTransaction());
    }
}
