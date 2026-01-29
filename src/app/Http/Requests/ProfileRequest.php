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
        return [
            'name'     => ['required', 'string', 'max:20'],
            'postcode' => ['required', 'string'],
            'address'  => ['required', 'string'],
            'building' => ['nullable', 'string'],
            'image'    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'ユーザー名を入力してください',
            'name.max'          => 'ユーザー名は20文字以内で入力してください',
            'postcode.required' => '郵便番号を入力してください',
            'address.required'  => '住所を入力してください',
            'image.image'       => '画像ファイルを選択してください',
            'image.mimes'       => '画像は jpg / jpeg / png のみ対応しています',
            'image.max'         => '画像サイズは2MB以内にしてください',
        ];
    }
}
