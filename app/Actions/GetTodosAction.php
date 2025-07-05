<?php

namespace App\Actions;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTodosAction
{
    /**
     * Todo一覧を取得
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function __invoke(Request $request): LengthAwarePaginator
    {
        $query = Todo::with('user')->where('user_id', $request->user()->id);

        // ステータスフィルタ
        if ($request->status) {
            if ($request->status === 'completed') {
                $query->where('completed', true);
            } elseif ($request->status === 'pending') {
                $query->where('completed', false);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }
} 