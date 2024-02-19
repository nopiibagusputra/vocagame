<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $decryptedBalance = decrypt($this->balance);

        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'balance' => $decryptedBalance,
        ];
    }
}
