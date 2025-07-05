<?php

namespace Tests\Unit\Actions;

use App\Actions\UpdateTodoAction;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTodoActionTest extends TestCase
{
    use RefreshDatabase;

    private UpdateTodoAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateTodoAction();
        $this->user = User::factory()->create();
    }

    /** 正常にTodoを更新できるテスト */
    public function test_updates_todo_successfully(): void
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => '元のタイトル',
            'content' => '元の内容',
            'completed' => false
        ]);

        $request = new UpdateTodoRequest();
        $request->merge([
            'title' => '更新されたタイトル',
            'content' => '更新された内容',
            'completed' => true
        ]);
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request, $todo->id);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals('更新されたタイトル', $result->title);
        $this->assertEquals('更新された内容', $result->content);
        $this->assertTrue($result->completed);
    }

    /** 部分更新ができるテスト */
    public function test_partial_update(): void
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => '元のタイトル',
            'content' => '元の内容',
            'completed' => false
        ]);

        // タイトルのみ更新
        $request = new UpdateTodoRequest();
        $request->merge(['title' => '更新されたタイトル']);
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request, $todo->id);

        $this->assertEquals('更新されたタイトル', $result->title);
        $this->assertEquals('元の内容', $result->content); // 変更されない
        $this->assertFalse($result->completed); // 変更されない
    }

    /** データベースが更新されるテスト */
    public function test_updates_database(): void
    {
        $todo = Todo::factory()->create([
            'user_id' => $this->user->id,
            'title' => '元のタイトル',
            'completed' => false
        ]);

        $request = new UpdateTodoRequest();
        $request->merge([
            'title' => '更新されたタイトル',
            'completed' => true
        ]);
        $request->setUserResolver(fn() => $this->user);

        ($this->action)($request, $todo->id);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '更新されたタイトル',
            'completed' => true,
            'user_id' => $this->user->id
        ]);
    }

    /** 他のユーザーのTodoは更新できないテスト */
    public function test_cannot_update_other_users_todo(): void
    {
        $otherUser = User::factory()->create();
        $otherTodo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $request = new UpdateTodoRequest();
        $request->merge(['title' => '更新されたタイトル']);
        $request->setUserResolver(fn() => $this->user);

        $this->expectException(ModelNotFoundException::class);

        ($this->action)($request, $otherTodo->id);
    }
} 