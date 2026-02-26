<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExhibitionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // 認証済みユーザーのみ許可するなら、ここで制御可能
    }

    public function rules()
    {
        return [
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer|min:0',
            'condition' => ['required', 'integer', Rule::in(array_keys(config('select_options.item_conditions')))],
            'image' => 'required|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'カテゴリーを選択してください。',
            'category_id.array' => 'カテゴリーの形式が正しくありません。',
            'category_id.*.exists' => '選択されたカテゴリーは存在しません。',
            'name.required' => '商品名を入力してください。',
            'name.string' => '商品名の形式が正しくありません。',
            'name.max' => '商品名は255文字以内で入力してください。',
            'brand.string' => 'ブランド名の形式が正しくありません。',
            'brand.max' => 'ブランド名は255文字以内で入力してください。',
            'description.required' => '商品の説明を入力してください。',
            'description.string' => '商品の説明の形式が正しくありません。',
            'price.required' => '価格を入力してください。',
            'price.integer' => '価格は整数で入力してください。',
            'price.min' => '価格は0以上で入力してください。',
            'condition.required' => '商品の状態を選択してください。',
            'condition.integer' => '商品の状態の形式が正しくありません。',
            'condition.in' => '選択された商品の状態は無効です。',
            'image.required' => '画像をアップロードしてください。',
            'image.image' => '画像ファイルの形式が正しくありません。',
            'image.max' => '画像ファイルのサイズは2MB以内でアップロードしてください。',
        ];
    }

}
