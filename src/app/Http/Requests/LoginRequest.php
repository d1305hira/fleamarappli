<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Fortify;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    return [
        Fortify::username() => 'required',
    'password' => 'required',
    ];
    }

    public function messages()
    {
        return [
            Fortify::username() . '.required' => 'メールアドレスを入力してください。',
                        'password.required' => 'パスワードを入力してください。',
];
}
}
