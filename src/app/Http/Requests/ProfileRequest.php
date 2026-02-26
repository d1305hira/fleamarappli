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
            'name' => 'required|string|max:255',
            'postal_code' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png', // ← 画像バリデーション
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名を入力してください。',
            'name.string' => 'ユーザー名の形式が正しくありません。',
            'name.max' => 'ユーザー名は255文字以内で入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号の形式が正しくありません。例: 123-4567',
            'address.required' => '住所を入力してください。',
            'address.string' => '住所の形式が正しくありません。',
            'address.max' => '住所は255文字以内で入力してください。',
            'building.string' => '建物名の形式が正しくありません。',
            'building.max' => '建物名は255文字以内で入力してください。',
            'image.image' => 'プロフィール画像は画像ファイルである必要があります。',
            'image.max' => 'プロフィール画像のサイズは2MB以内でアップロードしてください。',
            'image.mimes' => 'プロフィール画像はjpg、jpeg、png形式のファイルでアップロードしてください。',
        ];
    }
}
