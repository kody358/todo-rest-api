<?php

namespace App\Actions;

use App\Models\Todo;
use Illuminate\Http\Request;

class RestoreTodoAction
{
    /**
     * Todoを復元
     *
     * @param Request $request
     * @param int $id
     * @return Todo
     */
    public function __invoke(Request $request, int $id): Todo
    {
        $todo = Todo::onlyTrashed()->where('user_id', $request->user()->id)->findOrFail($id);
        $todo->restore();
        $todo->load('user');

        return $todo;
    }
} 