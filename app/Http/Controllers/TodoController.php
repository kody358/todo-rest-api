<?php

namespace App\Http\Controllers;

use App\Actions\CreateTodoAction;
use App\Actions\DeleteTodoAction;
use App\Actions\GetTodoAction;
use App\Actions\GetTodosAction;
use App\Actions\RestoreTodoAction;
use App\Actions\UpdateTodoAction;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class TodoController extends Controller
{
    /**
     * Todo一覧取得
     *
     * @OA\Get(
     *     path="/api/todos",
     *     summary="Todo一覧取得",
     *     description="認証されたユーザーのTodo一覧を取得します。",
     *     tags={"Todo"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="ステータスフィルタ (completed/pending)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"completed", "pending"})
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="ソート項目",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "updated_at", "title"})
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         description="ソート順",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="ページ番号",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="1ページあたりの項目数",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo一覧取得成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="タスクのタイトル"),
     *                         @OA\Property(property="content", type="string", example="タスクの内容"),
     *                         @OA\Property(property="completed", type="boolean", example=false),
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 ),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=100)
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
     * @param GetTodosAction $getTodosAction
     * @return JsonResponse
     */
    public function index(Request $request, GetTodosAction $getTodosAction): JsonResponse
    {
        $todos = $getTodosAction($request);

        return response()->json([
            'status' => 'success',
            'data' => $todos,
        ]);
    }

    /**
     * Todo作成
     *
     * @OA\Post(
     *     path="/api/todos",
     *     summary="Todo作成",
     *     description="新しいTodoを作成します",
     *     tags={"Todo"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="新しいタスク", description="タスクのタイトル"),
     *             @OA\Property(property="content", type="string", example="タスクの詳細内容", description="タスクの内容（任意）")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Todo作成成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Todoが作成されました"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="新しいタスク"),
     *                 @OA\Property(property="content", type="string", example="タスクの詳細内容"),
     *                 @OA\Property(property="completed", type="boolean", example=false),
     *                 @OA\Property(property="user_id", type="integer", example=1),
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
     * @param StoreTodoRequest $request
     * @param CreateTodoAction $createTodoAction
     * @return JsonResponse
     */
    public function store(StoreTodoRequest $request, CreateTodoAction $createTodoAction): JsonResponse
    {
        $todo = $createTodoAction($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Todoが作成されました',
            'data' => $todo,
        ], 201);
    }

    /**
     * Todo詳細取得
     *
     * @OA\Get(
     *     path="/api/todos/{id}",
     *     summary="Todo詳細取得",
     *     description="指定されたIDのTodo詳細を取得します",
     *     tags={"Todo"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="TodoのID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo詳細取得成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="タスクのタイトル"),
     *                 @OA\Property(property="content", type="string", example="タスクの内容"),
     *                 @OA\Property(property="completed", type="boolean", example=false),
     *                 @OA\Property(property="user_id", type="integer", example=1),
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todoが見つかりません",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Todo not found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @param GetTodoAction $getTodoAction
     * @return JsonResponse
     */
    public function show(Request $request, int $id, GetTodoAction $getTodoAction): JsonResponse
    {
        $todo = $getTodoAction($request, $id);

        return response()->json([
            'status' => 'success',
            'data' => $todo,
        ]);
    }

    /**
     * Todo更新
     *
     * @OA\Put(
     *     path="/api/todos/{id}",
     *     summary="Todo更新",
     *     description="指定されたIDのTodoを更新します",
     *     tags={"Todo"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="TodoのID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="更新されたタスク", description="タスクのタイトル"),
     *             @OA\Property(property="content", type="string", example="更新された内容", description="タスクの内容"),
     *             @OA\Property(property="completed", type="boolean", example=true, description="完了状態")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo更新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Todoが更新されました"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="更新されたタスク"),
     *                 @OA\Property(property="content", type="string", example="更新された内容"),
     *                 @OA\Property(property="completed", type="boolean", example=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todoが見つかりません",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Todo not found")
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
     * @param UpdateTodoRequest $request
     * @param int $id
     * @param UpdateTodoAction $updateTodoAction
     * @return JsonResponse
     */
    public function update(UpdateTodoRequest $request, int $id, UpdateTodoAction $updateTodoAction): JsonResponse
    {
        $todo = $updateTodoAction($request, $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Todoが更新されました',
            'data' => $todo,
        ]);
    }

    /**
     * Todo削除
     *
     * @OA\Delete(
     *     path="/api/todos/{id}",
     *     summary="Todo削除",
     *     description="指定されたIDのTodoを削除します（ソフトデリート）",
     *     tags={"Todo"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="TodoのID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo削除成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Todoが削除されました")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="認証が必要",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todoが見つかりません",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Todo not found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @param DeleteTodoAction $deleteTodoAction
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id, DeleteTodoAction $deleteTodoAction): JsonResponse
    {
        $deleteTodoAction($request, $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Todoが削除されました',
        ]);
    }

    /**
     * Todo復元
     *
     * @OA\Patch(
     *     path="/api/todos/{id}/restore",
     *     summary="Todo復元",
     *     description="削除されたTodoを復元します",
     *     tags={"Todo"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="TodoのID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo復元成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Todoが復元されました"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="復元されたタスク"),
     *                 @OA\Property(property="content", type="string", example="復元された内容"),
     *                 @OA\Property(property="completed", type="boolean", example=false),
     *                 @OA\Property(property="user_id", type="integer", example=1),
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todoが見つかりません",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Todo not found")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @param RestoreTodoAction $restoreTodoAction
     * @return JsonResponse
     */
    public function restore(Request $request, int $id, RestoreTodoAction $restoreTodoAction): JsonResponse
    {
        $todo = $restoreTodoAction($request, $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Todoが復元されました',
            'data' => $todo,
        ]);
    }
}
