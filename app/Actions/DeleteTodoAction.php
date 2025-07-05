<?php

namespace App\Actions;

use App\Models\Todo;
use Illuminate\Http\Request;

class DeleteTodoAction
{
    /**
     * Todoを削除
     *
     * @param Request $request
     * @param int $id
     * @return bool
     */
    public function __invoke(Request $request, int $id): bool
    {
        $todo = Todo::where('user_id', $request->user()->id)->findOrFail($id);
        
        return $todo->delete();
    }
} 