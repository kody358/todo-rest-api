<?php

namespace Tests\Unit\Actions;

use App\Actions\RegisterAction;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class RegisterActionTest extends TestCase
{
    use RefreshDatabase;

    private RegisterAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new RegisterAction();
    }

    /** 正常なユーザー登録のテスト */
    public function test_successful_user_registration(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $result = ($this->action)($request);

        // 結果の確認
        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertIsString($result['token']);

        // ユーザーがデータベースに保存されているか確認
        $user = $result['user'];
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // パスワードがハッシュ化されているか確認
        $dbUser = User::find($user->id);
        $this->assertTrue(Hash::check('password123', $dbUser->password));
    }

    /** ユーザー情報が正しく設定されることのテスト */
    public function test_user_data_is_correct(): void
    {
        $request = new RegisterRequest();
        $request->merge([
            'name' => '田中太郎',
            'email' => 'tanaka@example.com',
            'password' => 'securePassword456',
            'password_confirmation' => 'securePassword456',
        ]);

        $result = ($this->action)($request);
        $user = $result['user'];

        $this->assertEquals('田中太郎', $user->name);
        $this->assertEquals('tanaka@example.com', $user->email);
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
        $this->assertNotNull($user->id);
    }
} 