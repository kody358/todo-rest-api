<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用ユーザーを作成して認証
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_index(): void
    {
        $response = $this->getJson('/api/todos');
        
        $response->assertStatus(200);
    }

    public function test_store(): void
    {
        $todoData = [
            'title' => 'テストTodo',
            'content' => 'テスト内容'
        ];

        $response = $this->postJson('/api/todos', $todoData);
        
        $response->assertStatus(201);
    }

    public function test_show(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/todos/{$todo->id}");
        
        $response->assertStatus(200);
    }

    public function test_update(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);
        
        $updateData = [
            'title' => '更新されたTodo',
            'content' => '更新された内容',
            'completed' => true
        ];

        $response = $this->putJson("/api/todos/{$todo->id}", $updateData);
        
        $response->assertStatus(200);
    }

    public function test_destroy(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/todos/{$todo->id}");
        
        $response->assertStatus(200);
    }

    public function test_restore(): void
    {
        $todo = Todo::factory()->create(['user_id' => $this->user->id]);
        $todo->delete(); // ソフトデリート

        $response = $this->patchJson("/api/todos/{$todo->id}/restore");
        
        $response->assertStatus(200);
    }

    // 認証無しではアクセスできないことを検証
    public function test_unauthorized_access(): void
    {
        // このテストのためにSanctumの認証を無効にする
        $this->app['auth']->forgetGuards();
        
        $response = $this->getJson('/api/todos');
        
        $response->assertStatus(401);
    }
}
