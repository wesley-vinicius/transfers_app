<?php

namespace App\Domain\User\Models;

use App\Domain\User\Exceptions\InsuficientBalanceException;
use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'balance', 'user_id',
    ];

    public static function newFactory()
    {
        return WalletFactory::new();
    }

    public function deposit(float $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException(
                'The deposit amount must be greater than zero'
            );
        }
        $this->attributes['balance'] = floatval($this->balance + $value);
    }

    public function withdraw(float $value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException(
                'The withdrawal amount must be greater than zero'
            );
        }
        if (! $this->hasEnoughBalance($value)) {
            throw new InsuficientBalanceException(
                'The user have insufficient balance to withdraw'
            );
        }
        $this->attributes['balance'] = $this->balance -= $value;
    }

    private function hasEnoughBalance(float $value): bool
    {
        return $this->balance >= $value;
    }
}
