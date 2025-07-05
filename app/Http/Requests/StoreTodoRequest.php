<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTodoRequest extends FormRequest
{
    /**
     * リクエストの認可を実行
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'content' => 'nullable|string|max:1000',
        ];
    }

    /**
     * バリデーションエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須項目です',
            'title.string' => 'タイトルは文字列で入力してください',
            'title.max' => 'タイトルは100文字以内で入力してください',
            'content.string' => '内容は文字列で入力してください',
            'content.max' => '内容は1000文字以内で入力してください',
        ];
    }
}
