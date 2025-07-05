<?php

namespace App\Actions;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * ログイン処理
     *
     * @param LoginRequest $request
     * @return array
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): array
    {
        // バリデーション済みデータを取得（テスト環境でも動作するように）
        $data = $request->only(['email', 'password']);
        
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['認証情報が正しくありません']
            ]);
        }

        // 既存のトークンを削除
        $user->tokens()->delete();

        // 新しいトークンを作成
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
} 