<?php

namespace Tests\Unit\Actions;

use App\Actions\GetTodosAction;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class GetTodosActionTest extends TestCase
{
    use RefreshDatabase;

    private GetTodosAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetTodosAction();
        $this->user = User::factory()->create();
    }

    /** 認証されたユーザーのTodoのみ取得できるテスト */
    public function test_gets_authenticated_user_todos_only(): void
    {
        $otherUser = User::factory()->create();
        
        // 認証ユーザーのTodo
        Todo::factory()->count(3)->create(['user_id' => $this->user->id]);
        
        // 他のユーザーのTodo
        Todo::factory()->count(2)->create(['user_id' => $otherUser->id]);

        $request = Request::create('/todos');
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(3, $result->items());
        
        // 全てのTodoが認証ユーザーのものであることを確認
        foreach ($result->items() as $todo) {
            $this->assertEquals($this->user->id, $todo->user_id);
        }
    }

    /** ステータスフィルタ：完了済みのテスト */
    public function test_filters_completed_todos(): void
    {
        // 完了済みと未完了のTodoを作成
        Todo::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true
        ]);
        Todo::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);

        $request = Request::create('/todos?status=completed');
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request);

        $this->assertCount(2, $result->items());
        
        // 全てのTodoが完了済みであることを確認
        foreach ($result->items() as $todo) {
            $this->assertTrue($todo->completed);
        }
    }

    /** ステータスフィルタ：未完了のテスト */
    public function test_filters_pending_todos(): void
    {
        // 完了済みと未完了のTodoを作成
        Todo::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'completed' => true
        ]);
        Todo::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'completed' => false
        ]);

        $request = Request::create('/todos?status=pending');
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request);

        $this->assertCount(3, $result->items());
        
        // 全てのTodoが未完了であることを確認
        foreach ($result->items() as $todo) {
            $this->assertFalse($todo->completed);
        }
    }
} 