<?php

namespace App\Domain\Transaction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payer' => ['bail', 'required', 'integer', 'exists:users,id'],
            'payee' => ['bail', 'required', 'integer', 'different:payer', 'exists:users,id'],
            'value' => ['required', 'gt:0', 'numeric', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'value.min' => 'The transfer amount must be greater than zero',
            'payee.different' => 'The transfer cannot be made to the same user',
            'payer.exists' => 'There is no payment with this id',
            'payee.exists' => 'There is no beneficiary with this id',
        ];
    }
}
