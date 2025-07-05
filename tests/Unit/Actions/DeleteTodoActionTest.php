<?php

namespace Tests\Unit\Actions;

use App\Actions\DeleteTodoAction;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class DeleteTodoActionTest extends TestCase
{
    use RefreshDatabase;

    private DeleteTodoAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new DeleteTodoAction();
        $this->user = User::factory()->create();
    }

    /** 正常にTodoを削除できるテスト */
    public function test_deletes_todo_successfully(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $request = Request::create('/todos/' . $todo->id, 'DELETE');
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request, $todo->id);

        $this->assertTrue($result);
    }

    /** ソフトデリートが実行されるテスト */
    public function test_performs_soft_delete(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $request = Request::create('/todos/' . $todo->id, 'DELETE');
        $request->setUserResolver(fn() => $this->user);

        ($this->action)($request, $todo->id);

        // 通常のクエリでは取得されない
        $this->assertNull(Todo::find($todo->id));

        // withTrashedでは取得できる
        $this->assertNotNull(Todo::withTrashed()->find($todo->id));

        // データベースには残っている
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
        ]);
    }

    /** deleted_atが設定されるテスト */
    public function test_sets_deleted_at_timestamp(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $beforeDeletion = now()->subSecond(); // 1秒前
        
        $request = Request::create('/todos/' . $todo->id, 'DELETE');
        $request->setUserResolver(fn() => $this->user);

        ($this->action)($request, $todo->id);

        $afterDeletion = now()->addSecond(); // 1秒後

        $deletedTodo = Todo::withTrashed()->find($todo->id);
        $this->assertNotNull($deletedTodo->deleted_at);
        $this->assertTrue($deletedTodo->deleted_at->between($beforeDeletion, $afterDeletion));
    }

    /** 他のユーザーのTodoは削除できないテスト */
    public function test_cannot_delete_other_users_todo(): void
    {
        $otherUser = User::factory()->create();
        $otherTodo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $request = Request::create('/todos/' . $otherTodo->id, 'DELETE');
        $request->setUserResolver(fn() => $this->user);

        $this->expectException(ModelNotFoundException::class);

        ($this->action)($request, $otherTodo->id);
    }
} 