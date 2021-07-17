<?php

namespace App\Domain\Transaction\Models;

use App\Domain\User\Models\User;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    use HasFactory;

    protected $table = 'transactions';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payer_id',
        'payee_id',
        'value',
    ];

    public function setValueAttribute($value): void
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('The transfer amount must be greater than zero');
        }
        $this->attributes['value'] = $value;
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id', 'id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id', 'id');
    }

    private static function newFactory()
    {
        return TransactionFactory::new();
    }
}
