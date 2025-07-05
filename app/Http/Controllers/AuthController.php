<?php

namespace App\Http\Controllers;

use App\Actions\LoginAction;
use App\Actions\LogoutAction;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * ログイン
     *
     * @param LoginRequest $request
     * @param LoginAction $loginAction
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginAction $loginAction): JsonResponse
    {
        $result = $loginAction($request);

        return response()->json([
            'status' => 'success',
            'message' => 'ログインしました',
            'data' => $result,
        ]);
    }

    /**
     * ログアウト
     *
     * @param Request $request
     * @param LogoutAction $logoutAction
     * @return JsonResponse
     */
    public function logout(Request $request, LogoutAction $logoutAction): JsonResponse
    {
        $logoutAction($request);

        return response()->json([
            'status' => 'success',
            'message' => 'ログアウトしました',
        ]);
    }

    /**
     * ユーザー情報取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }
}
