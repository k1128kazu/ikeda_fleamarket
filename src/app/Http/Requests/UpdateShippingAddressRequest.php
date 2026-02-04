<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingAddressRequest extends FormRequest
{
    public function authorize()
    {
         return true;
    }

    public function rules()
    {
        return [
            'postcode' => ['required'],
            'address'  => ['required'],
            'building' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'postcode.required' => '郵便番号を入力してください。',
            'address.required'  => '住所を入力してください。',
        ];
    }
}
