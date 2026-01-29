<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function rules()
    {
        return [
            'postcode' => ['required'],
            'address'  => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'postcode.required' => '郵便番号を入力してください',
            'address.required'  => '住所を入力してください',
        ];
    }
}
