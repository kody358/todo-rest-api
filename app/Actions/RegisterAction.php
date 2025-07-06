<?php

namespace App\Actions;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterAction
{
    /**
     * ユーザー登録処理
     *
     * @param RegisterRequest $request
     * @return array
     */
    public function __invoke(RegisterRequest $request): array
    {
        $data = $request->only(['name', 'email', 'password']);
        
        // ユーザーを作成
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // 認証トークンを作成
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
} 