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
            'brand'        => ['nullable', 'string', 'max:255'],
            'description'  => ['required', 'max:255'],
            'price'        => ['required', 'integer', 'min:0'],
            'image'        => ['required', 'image', 'mimes:jpeg,png', 'max:1024',],

            // ★ 状態は4種に制限（確定）
            'condition'    => [
                'required',
                'string',
                'in:良好,目立った傷や汚れなし,やや傷や汚れあり,状態が悪い',
            ],

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
            'image.max'             => '画像サイズが大きすぎます（1MB以内にしてください）',

            // ★ 正しいフィールドのみ残す
            'condition.required'    => '商品の状態を選択してください',
            'condition.in'          => '商品の状態を正しく選択してください',

            'categories.required'   => 'カテゴリを選択してください',
            'categories.*.exists'   => '正しいカテゴリを選択してください',
        ];
    }
}
