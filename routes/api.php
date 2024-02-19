<?php
namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
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

Route::post('/login', [Auth\AuthController::class, 'Login'])->name('login');

Route::prefix('/v1')->middleware('auth:api')->group(function () {
    Route::get('users', [User\UserController::class, 'index'])->name('user.list');
    Route::post('users', [User\UserController::class,'store'])->name('user.store');
    Route::put('users/{id}', [User\UserController::class, 'update'])->name('user.update');
    Route::delete('users/{id}', [User\UserController::class, 'destroy'])->name('user.delete');

    Route::post('logout', [Auth\AuthController::class, 'logout'])->name('logout');

    Route::get('products', [Products\ProductsController::class, 'index'])->name('products.list');
    Route::post('products', [Products\ProductsController::class, 'store'])->name('products.store');
    Route::put('products/{id}', [Products\ProductsController::class, 'update'])->name('products.update');
    Route::delete('products/{id}', [Products\ProductsController::class, 'destroy'])->name('products.delete');
    
    Route::post('wallet', [Wallet\WalletController::class, 'createWallet'])->name('wallet.store');
    Route::post('wallet/data', [Wallet\WalletController::class, 'getWallet'])->name('wallet.data');
    Route::post('wallet/topup', [Wallet\WalletController::class, 'depositBalances'])->name('wallet.topup');
    Route::post('wallet/withdrawal', [Wallet\WalletController::class, 'withdrawalBalance'])->name('wallet.withdrawal');

    Route::post('transaction', [Transaction\TransactionController::class, 'buyItems'])->name('transaction.buyitems');

});
