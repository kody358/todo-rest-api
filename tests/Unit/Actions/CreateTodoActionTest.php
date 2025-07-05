<?php

namespace Tests\Unit\Actions;

use App\Actions\CreateTodoAction;
use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTodoActionTest extends TestCase
{
    use RefreshDatabase;

    private CreateTodoAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateTodoAction();
        $this->user = User::factory()->create();
    }

    /** 正常なTodo作成のテスト */
    public function test_creates_todo_successfully(): void
    {
        $request = new StoreTodoRequest();
        $request->merge([
            'title' => 'テストタイトル',
            'content' => 'テスト内容'
        ]);
        $request->setUserResolver(fn() => $this->user);

        $todo = ($this->action)($request);

        $this->assertInstanceOf(Todo::class, $todo);
        $this->assertEquals('テストタイトル', $todo->title);
        $this->assertEquals('テスト内容', $todo->content);
        $this->assertEquals($this->user->id, $todo->user_id);
        $this->assertFalse($todo->completed); // デフォルトはfalse
    }

    /** データベースに保存されるテスト */
    public function test_saves_todo_to_database(): void
    {
        $request = new StoreTodoRequest();
        $request->merge([
            'title' => 'テストタイトル',
            'content' => 'テスト内容'
        ]);
        $request->setUserResolver(fn() => $this->user);

        $todo = ($this->action)($request);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => 'テストタイトル',
            'content' => 'テスト内容',
            'user_id' => $this->user->id,
            'completed' => false
        ]);
    }

    /** content未指定でも作成できるテスト */
    public function test_creates_todo_without_content(): void
    {
        $request = new StoreTodoRequest();
        $request->merge([
            'title' => 'タイトルのみ'
        ]);
        $request->setUserResolver(fn() => $this->user);

        $todo = ($this->action)($request);

        $this->assertEquals('タイトルのみ', $todo->title);
        $this->assertNull($todo->content);
        $this->assertEquals($this->user->id, $todo->user_id);
    }
} 