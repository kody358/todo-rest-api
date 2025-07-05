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
        $data = $request->only(['title', 'content']);

        // 認証されたユーザーのIDを設定
        $data['user_id'] = $request->user()->id;
        
        // completedのデフォルト値を設定
        $data['completed'] = $data['completed'] ?? false;

        $todo = Todo::create($data);
        $todo->load('user');

        return $todo;
    }
} 