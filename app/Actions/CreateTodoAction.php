<?php

namespace App\Actions;

use App\Models\Todo;
use App\Http\Requests\StoreTodoRequest;

class CreateTodoAction
{
    /**
     * Todoを作成
     *
     * @param StoreTodoRequest $request
     * @return Todo
     */
    public function __invoke(StoreTodoRequest $request): Todo
    {
        $validated = $request->validated();

        // 認証されたユーザーのIDを設定
        $validated['user_id'] = $request->user()->id;

        $todo = Todo::create($validated);
        $todo->load('user');

        return $todo;
    }
} 