<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $preview_url = $this->input('preview_url');

        $rules = [
            'name' => ['required', 'max:255'],
            'brand' => ['nullable', 'max:255'],
            'description'  => ['required', 'max:255'],
            'price' => ['required', 'min:0','max:99999999','numeric'],
            'condition_id' => ['required'],
            'category_id' => ['required'],
        ];

        if ($preview_url) {
            $rules['image'] = ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'];
        } else {
            $rules['image'] = ['required', 'image', 'mimes:jpeg,png', 'max:2048'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください',
            'name.max'      => 'ユーザー名を255字以内で入力してください',
            'brand.max'      => 'ブランドを255字以内で入力してください',
            'description.required'  => '商品説明を入力してください',
            'description.max'       => '商品説明を255字以内で入力してください',
            'price.required' => '価格を入力してください',
            'price.min' => '価格は0円以上で入力してください',
            'price.max' => '価格は999999999円以下で入力してください',
            'price.numeric' => '価格を数値で入力してください',
            'condition_id.required' => '状態を選択してください',
            'category_id.required' => 'カテゴリーを選択してください',
            'image.required'    => '商品画像を登録してください',
            'image.image'       => '画像ファイルを選択してください',
            'image.mimes'       => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.max'         => '画像サイズが大きすぎます(2MBまで)',
        ];
    }



}
