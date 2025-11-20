<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'purchase_method_id' => ['required'],
            'is_filled_with_delivery_address' => ['required', 'in:1,true', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'purchase_method_id.required' => '購入方法を入力してください',
            'is_filled_with_delivery_address.required' => '配送先を登録してください',
            'is_filled_with_delivery_address.in' => '配送先を登録してください',
            'is_filled_with_delivery_address.boolean' => '値がbooleanでありません',
        ];
    }
}
