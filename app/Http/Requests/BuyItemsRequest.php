<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class BuyItemsRequest extends FormRequest
{

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $postData = $this->all();
    
        if (Auth::user()->id !== $postData['userId']) {
            abort(403, 'Access Denied, ID not match');
        }
    }

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'productId'     =>'required',
            'userId'        =>'required',
            'methodPayment' =>'required',
            'amount'        =>'required|numeric',
        ];
    }
}
