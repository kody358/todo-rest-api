<?php

namespace Tests\Unit\Actions;

use App\Actions\RestoreTodoAction;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class RestoreTodoActionTest extends TestCase
{
    use RefreshDatabase;

    private RestoreTodoAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RestoreTodoAction();
        $this->user = User::factory()->create();
    }

    /** 正常にTodoを復元できるテスト */
    public function test_restores_todo_successfully(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);
        $todo->delete(); // ソフトデリート

        $request = Request::create('/todos/' . $todo->id . '/restore', 'PATCH');
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request, $todo->id);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($todo->id, $result->id);
        $this->assertNull($result->deleted_at);
    }

    /** 削除されていないTodoは復元できないテスト */
    public function test_cannot_restore_non_deleted_todo(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);
        // 削除しない

        $request = Request::create('/todos/' . $todo->id . '/restore', 'PATCH');
        $request->setUserResolver(fn() => $this->user);

        $this->expectException(ModelNotFoundException::class);

        ($this->action)($request, $todo->id);
    }

    /** deleted_atがnullになるテスト */
    public function test_clears_deleted_at_timestamp(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);
        $todo->delete(); // ソフトデリート

        $deletedTodo = Todo::withTrashed()->find($todo->id);
        $this->assertNotNull($deletedTodo->deleted_at);

        $request = Request::create('/todos/' . $todo->id . '/restore', 'PATCH');
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request, $todo->id);

        $this->assertNull($result->deleted_at);
    }
} 