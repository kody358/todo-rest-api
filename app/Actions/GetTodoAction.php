<?php

namespace App\Actions;

use App\Models\Todo;
use Illuminate\Http\Request;

class GetTodoAction
{
    /**
     * Todo詳細を取得
     *
     * @param Request $request
     * @param int $id
     * @return Todo
     */
    public function __invoke(Request $request, int $id): Todo
    {
        return Todo::with('user')->where('user_id', $request->user()->id)->findOrFail($id);
    }
} 