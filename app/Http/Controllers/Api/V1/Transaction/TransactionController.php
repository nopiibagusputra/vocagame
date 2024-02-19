<?php

namespace App\Http\Controllers\Api\V1\Transaction;

use Illuminate\Support\Facades\Crypt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\BuyItemsRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Product;
use DB;

class TransactionController extends Controller
{
    /**
     * Buy items
     *
     * @param BuyItemsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyItems(BuyItemsRequest $request){
        $wallet = Wallet::where('userId', $request->userId)->first();
        $product = Product::where('id', $request->productId)->first();
        $balance = decrypt($wallet->balance) - ($product->prices * $request->amount);;
        
        if($product == null OR $product->available == 0){
            return response()->json([
                'message' => 'Product not available'
            ], 404);
        }
        
        if($balance < 0){
            return response()->json([
              'message' => 'Insufficient balance'
               ], 404);
        }

        DB::transaction(function () use ($wallet, $balance, $request) {
            $wallet->balance = Crypt::encrypt($balance);
            $wallet->save();
            
            Transaction::create([
                'userId'        => $request->userId,
                'productId'     => $request->productId,
                'methodPayment' => $request->methodPayment,
                'amount'        => $request->amount
            ]);
        });

        return response()->json([
            'wallet' => new TransactionResource($wallet),
            'message' => 'Successfully purchased product'
        ], 201);
    }
}
