<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

        public function rules()
    {
        $rules = [
            'description' => ['required', 'string','max:255'],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'description.required' => 'コメントを入力してください',
            'description.max'      => 'コメントを255字以内で入力してください',
        ];
    }
}
