<?php

namespace App\Http\Controllers;

use App\Actions\LoginAction;
use App\Actions\LogoutAction;
use App\Actions\RegisterAction;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    /**
     * ユーザー登録
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="ユーザー登録",
     *     description="新しいユーザーを登録し、認証トークンを取得します",
     *     tags={"認証"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="田中太郎", description="ユーザー名"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="メールアドレス"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", description="パスワード（8文字以上）"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="パスワード確認")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="登録成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="ユーザー登録が完了しました"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="田中太郎"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="バリデーションエラー",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     *
     * @param RegisterRequest $request
     * @param RegisterAction $registerAction
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, RegisterAction $registerAction): JsonResponse
    {
        $result = $registerAction($request);

        return response()->json([
            'status' => 'success',
            'message' => 'ユーザー登録が完了しました',
            'data' => $result,
        ], 201);
    }

    /**
     * ログイン
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="ユーザーログイン",
     *     description="メールアドレスとパスワードでユーザーログインを行い、認証トークンを取得します",
     *     tags={"認証"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com", description="メールアドレス"),
     *             @OA\Property(property="password", type="string", format="password", example="password", description="パスワード")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="ログイン成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="ログインしました"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="ユーザー名"),
     *                     @OA\Property(property="email", type="string", example="user@example.com"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="1|abcdef123456...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="認証失敗",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="認証に失敗しました")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="バリデーションエラー",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/logout",
     *     summary="ユーザーログアウト",
     *     description="現在のユーザーをログアウトし、認証トークンを無効化します",
     *     tags={"認証"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="ログアウト成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="ログアウトしました")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="認証が必要",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/user",
     *     summary="ユーザー情報取得",
     *     description="現在認証されているユーザーの情報を取得します",
     *     tags={"認証"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="ユーザー情報取得成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="ユーザー名"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="認証が必要",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
