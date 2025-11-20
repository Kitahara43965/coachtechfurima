<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'delivery_postcode' => ['required', 'regex:/^(?=[\d-]{8}$).*-.*/'],
            'delivery_address'  => ['required', 'max:255'],
            'delivery_building' => ['nullable', 'max:255'],
        ];
        
        return $rules;
    }

    public function messages()
    {
        return [
            'delivery_postcode.required' => '郵便番号を入力してください',
            'delivery_postcode.regex'    => 'ハイフンありの8文字で入力してください',
            'delivery_address.required'  => '住所を入力してください',
            'delivery_address.max'       => '住所を255字以内で入力してください',
            'delivery_building.max'       => '建物名を255字以内で入力してください',
        ];
    }
}
