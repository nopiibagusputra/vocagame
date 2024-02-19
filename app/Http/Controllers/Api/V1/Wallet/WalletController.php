<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AddWalletRequest;
use App\Http\Requests\DepositWalletRequest;
use App\Http\Requests\WithdrawalWalletRequest;
use App\Http\Resources\WalletResource;
use App\Models\Wallet;
use App\Models\User;
use App\Models\Topup;
use App\Models\Withdrawal;
use DB;

class WalletController extends Controller
{
    /**
     * Creates a new wallet for the given user.
     *
     * @param AddWalletRequest $request
     * @return JsonResponse
     */
    public function createWallet(AddWalletRequest $request){
        $user = User::find($request->userId);
    
        if($user == null){
            return response()->json([
                'message' => 'Account not found'
            ], 404);
        }
    
        $wallet = Wallet::where('userId', $request->userId)->first();
    
        if($wallet != null){
            return response()->json([
                'message' => 'Wallet already exists'
            ], 409);
        }
    
        Wallet::create([
            'userId' => $request->userId,
            'balance' => 0
        ]);
    
        return response()->json([
            'message' => 'Wallet created successfully'
        ], 201);
    }

    /**
     * Deposits the given balance to the specified wallet.
     *
     * @param DepositWalletRequest $request
     * @return JsonResponse
     */
    public function depositBalances(DepositWalletRequest $request){
        $wallet = Wallet::where('id', $request->walletId)->first();
    
        if($wallet == null){
            return response()->json([
               'message' => 'Wallet not found'
               ], 404);
        }
    
        $userId = $wallet->userId;
        $balance = $wallet->balance;
        $topup = $balance + $request->balance;
    
        DB::transaction(function () use ($wallet, $topup, $userId, $request) {
            $wallet->balance = $topup;
            $wallet->save();
    
            Topup::create([
                'userId' => $userId,
                'amount' => $request->balance
            ]);
        });

        return response()->json([
            'wallet' => new WalletResource($wallet)
        ], 200);
    }

    /**
     * Withdraws the specified amount from the specified wallet.
     *
     * @param int $amount The amount to withdraw.
     * @param int $walletId The ID of the wallet to withdraw from.
     * @return JsonResponse
     */
    public function withdrawalBalance(WithdrawalWalletRequest $request){
        $wallet = Wallet::where('id', $request->walletId)->first();

        if($wallet == null){
            return response()->json([
               'message' => 'Wallet not found'
               ], 404);
        }

        $balance = $wallet->balance;
        $withdrawal = $balance - $request->amount;
        
        if($withdrawal < 0){
            return response()->json([
              'message' => 'Insufficient balance'
               ], 404);
        }

        DB::transaction(function () use ($wallet, $withdrawal, $request) {
            Withdrawal::create([
                'walletId'      => $request->walletId,
                'amount'        => $request->amount,
                'bank_name'     => $request->bank_name,
                'account_number' => $request->account_number,
                'status'        => 'Approved'
            ]);
    
            $wallet->balance = $withdrawal;
            $wallet->save();
        });

        return response()->json([
            'wallet' => new WalletResource($wallet)
        ], 200);

    }
}
