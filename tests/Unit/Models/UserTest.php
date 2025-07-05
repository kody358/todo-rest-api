<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** Todoとのリレーションのテスト */
    public function test_has_many_todos(): void
    {
        $user = User::factory()->create();

        // リレーションの型チェック
        $this->assertInstanceOf(HasMany::class, $user->todos());
    }

    /** Todoとのリレーション動作テスト */
    public function test_todos_relationship_works(): void
    {
        $user = User::factory()->create();
        
        // ユーザーに複数のTodoを作成
        $todos = Todo::factory()->count(3)->create(['user_id' => $user->id]);

        // リレーションでTodoを取得
        $userTodos = $user->todos;

        $this->assertCount(3, $userTodos);
        
        // 各TodoがUserに属していることを確認
        foreach ($userTodos as $todo) {
            $this->assertEquals($user->id, $todo->user_id);
        }
    }

    /** 他のユーザーのTodoは取得されないテスト */
    public function test_todos_relationship_isolation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        // user1に2つのTodo
        Todo::factory()->count(2)->create(['user_id' => $user1->id]);
        
        // user2に3つのTodo
        Todo::factory()->count(3)->create(['user_id' => $user2->id]);

        // 各ユーザーは自分のTodoのみ取得
        $this->assertCount(2, $user1->todos);
        $this->assertCount(3, $user2->todos);
    }
} 