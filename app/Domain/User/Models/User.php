<?php

namespace App\Domain\User\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'document',
        'user_type_id',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    private static function newFactory()
    {
        return UserFactory::new();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
