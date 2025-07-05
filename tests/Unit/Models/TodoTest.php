<?php

namespace Tests\Unit\Models;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;

    /** ユーザーとのリレーションのテスト */
    public function test_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $todo = Todo::factory()->create(['user_id' => $user->id]);

        // リレーションの型チェック
        $this->assertInstanceOf(BelongsTo::class, $todo->user());

        // 実際のユーザー取得テスト
        $this->assertEquals($user->id, $todo->user->id);
        $this->assertEquals($user->name, $todo->user->name);
        $this->assertEquals($user->email, $todo->user->email);
    }

    /** completedスコープのテスト */
    public function test_completed_scope(): void
    {
        $user = User::factory()->create();
        
        // 完了済みと未完了のTodoを作成
        $completedTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed' => true
        ]);
        $pendingTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed' => false
        ]);

        // completedスコープのテスト
        $completedTodos = Todo::completed()->get();

        $this->assertCount(1, $completedTodos);
        $this->assertTrue($completedTodos->first()->completed);
        $this->assertEquals($completedTodo->id, $completedTodos->first()->id);
    }

    /** pendingスコープのテスト */
    public function test_pending_scope(): void
    {
        $user = User::factory()->create();
        
        // 完了済みと未完了のTodoを作成
        $completedTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed' => true
        ]);
        $pendingTodo = Todo::factory()->create([
            'user_id' => $user->id,
            'completed' => false
        ]);

        // pendingスコープのテスト
        $pendingTodos = Todo::pending()->get();

        $this->assertCount(1, $pendingTodos);
        $this->assertFalse($pendingTodos->first()->completed);
        $this->assertEquals($pendingTodo->id, $pendingTodos->first()->id);
    }
} 