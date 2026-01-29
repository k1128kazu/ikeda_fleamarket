<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'         => ['required'],
            'description'  => ['required', 'max:255'],
            'price'        => ['required', 'integer', 'min:0'],
            'image'        => ['required', 'image', 'mimes:jpeg,png'],
            'status'       => ['required'],
            'categories'   => ['required', 'array'],
            'categories.*' => ['exists:categories,id'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => '商品名を入力してください',
            'description.required'  => '商品説明を入力してください',
            'description.max'       => '商品説明は255文字以内で入力してください',
            'price.required'        => '価格を入力してください',
            'price.integer'         => '価格は数値で入力してください',
            'price.min'             => '価格は0円以上で入力してください',
            'image.required'        => '商品画像を選択してください',
            'image.image'           => '画像ファイルを選択してください',
            'image.mimes'           => '画像はjpegまたはpng形式で選択してください',
            'status.required'       => '商品の状態を選択してください',
            'categories.required'   => 'カテゴリを選択してください',
            'categories.*.exists'   => '正しいカテゴリを選択してください',
        ];
    }
}
