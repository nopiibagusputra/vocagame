<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AddWalletRequest;
use App\Http\Requests\WalletRequest;
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
            'balance' => Crypt::encrypt(0)
        ]);
    
        return response()->json([
            'message' => 'Wallet created successfully'
        ], 201);
    }
    
    /**
     * Get a user's wallet.
     *
     * @param WalletRequest $request
     * @return JsonResponse
     */
    public function getWallet(WalletRequest $request){
        $user = Wallet::where('userId', $request->userId)->first();
    
        if($user == null){
            return response()->json([
               'message' => 'Wallet not found'
               ], 404);
        }

        return response()->json([
            'wallet' => new WalletResource($user)
        ], 200);
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
        $balance = decrypt($wallet->balance);
        $calc = $balance + $request->balance;
        $topup = Crypt::encrypt($calc);
    
        DB::transaction(function () use ($wallet, $topup, $userId, $request) {
            $wallet->balance = $topup;
            $wallet->save();
    
            Topup::create([
                'userId' => $userId,
                'amount' => Crypt::encrypt($request->balance)
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

        $balance = decrypt($wallet->balance);
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
    
            $wallet->balance = Crypt::encrypt($withdrawal);
            $wallet->save();
        });

        return response()->json([
            'wallet' => new WalletResource($wallet)
        ], 200);

    }
}
