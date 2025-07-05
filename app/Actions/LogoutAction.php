<?php

namespace App\Actions;

use Illuminate\Http\Request;

class LogoutAction
{
    /**
     * ログアウト処理
     *
     * @param Request $request
     * @return bool
     */
    public function __invoke(Request $request): bool
    {
        $request->user()->currentAccessToken()->delete();
        
        return true;
    }
} 