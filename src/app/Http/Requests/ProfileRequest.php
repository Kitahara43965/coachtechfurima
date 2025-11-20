<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'username' => ['required', 'max:20'],
            'postcode' => ['required', 'regex:/^(?=[\d-]{8}$).*-.*/'],
            'address'  => ['required', 'max:255'],
            'building' => ['nullable', 'max:255'],
        ];

        if ($this->input('preview_url')) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'];
        } else {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'];
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'username.required' => 'ユーザー名を入力してください',
            'username.max'      => 'ユーザー名を20字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'postcode.regex'    => 'ハイフンありの8文字で入力してください',
            'address.required'  => '住所を入力してください',
            'address.max'       => '住所を255字以内で入力してください',
            'building.max'      => '建物名を255字以内で入力してください',
            'image.image'       => '画像ファイルを選択してください',
            'image.mimes'       => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max'         => '画像サイズが大きすぎます(2MBまで)',
        ];
    }
}