<?php

namespace App\Actions;

use App\Models\Todo;
use App\Http\Requests\UpdateTodoRequest;

class UpdateTodoAction
{
    /**
     * Todoを更新
     *
     * @param UpdateTodoRequest $request
     * @param int $id
     * @return Todo
     */
    public function __invoke(UpdateTodoRequest $request, int $id): Todo
    {
        $todo = Todo::where('user_id', $request->user()->id)->findOrFail($id);
        
        $data = $request->only(['title', 'content', 'completed']);

        $todo->update($data);
        $todo->load('user');

        return $todo;
    }
} 