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

class TodoController extends Controller
{
    /**
     * Todo一覧取得
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
