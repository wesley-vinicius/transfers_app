<?php

use App\Domain\Transaction\Http\Controllers\TransactionController;
use App\Domain\User\Http\Controllers\CreateUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user', [CreateUserController::class, 'execute'])->name('user.create');
Route::post('/transaction', [TransactionController::class, 'execute'])->name('transaction');