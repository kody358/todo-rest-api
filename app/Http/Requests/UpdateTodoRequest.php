<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
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
            'title' => 'sometimes|required',
            'content' => 'nullable',
            'completed' => 'sometimes|boolean',
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
            'completed.boolean' => '完了状態は真偽値で入力してください',
        ];
    }
}
