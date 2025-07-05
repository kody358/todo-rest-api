<?php

namespace Tests\Unit\Actions;

use App\Actions\GetTodoAction;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class GetTodoActionTest extends TestCase
{
    use RefreshDatabase;

    private GetTodoAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetTodoAction();
        $this->user = User::factory()->create();
    }

    /** 正常にTodoを取得できるテスト */
    public function test_gets_todo_successfully(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $request = Request::create('/todos/' . $todo->id);
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request, $todo->id);

        $this->assertInstanceOf(Todo::class, $result);
        $this->assertEquals($todo->id, $result->id);
        $this->assertEquals($todo->title, $result->title);
        $this->assertEquals($todo->content, $result->content);
        $this->assertEquals($this->user->id, $result->user_id);
    }

    /** 他のユーザーのTodoは取得できないテスト */
    public function test_cannot_get_other_users_todo(): void
    {
        $otherUser = User::factory()->create();
        $otherTodo = Todo::factory()->create(['user_id' => $otherUser->id]);

        $request = Request::create('/todos/' . $otherTodo->id);
        $request->setUserResolver(fn() => $this->user);

        $this->expectException(ModelNotFoundException::class);

        ($this->action)($request, $otherTodo->id);
    }

    /** ソフトデリートされたTodoは取得できないテスト */
    public function test_cannot_get_soft_deleted_todo(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);
        $todo->delete(); // ソフトデリート

        $request = Request::create('/todos/' . $todo->id);
        $request->setUserResolver(fn() => $this->user);

        $this->expectException(ModelNotFoundException::class);

        ($this->action)($request, $todo->id);
    }

    /** 認証ユーザーのTodoのみフィルタされるテスト */
    public function test_filters_by_authenticated_user_only(): void
    {
        $user2 = User::factory()->create();
        
        // 同じIDで異なるユーザーのTodoを作成（理論的には不可能だが、テストとして）
        $userTodo = Todo::factory()->create(['user_id' => $this->user->id]);
        $otherTodo = Todo::factory()->create(['user_id' => $user2->id]);

        // 認証ユーザーのTodo
        $request1 = Request::create('/todos/' . $userTodo->id);
        $request1->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request1, $userTodo->id);
        $this->assertEquals($this->user->id, $result->user_id);

        // 他のユーザーのTodoは取得できない
        $request2 = Request::create('/todos/' . $otherTodo->id);
        $request2->setUserResolver(fn() => $this->user);

        $this->expectException(ModelNotFoundException::class);
        ($this->action)($request2, $otherTodo->id);
    }
} 