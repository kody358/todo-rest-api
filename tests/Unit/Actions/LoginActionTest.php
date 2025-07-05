<?php

namespace Tests\Unit\Actions;

use App\Actions\LoginAction;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LoginActionTest extends TestCase
{
    use RefreshDatabase;

    private LoginAction $action;
    private User $user;
    private string $password = 'password123';

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new LoginAction();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make($this->password)
        ]);
    }

    /** 正常なログインのテスト */
    public function test_successful_login(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'email' => 'test@example.com',
            'password' => $this->password
        ]);

        $result = ($this->action)($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertInstanceOf(User::class, $result['user']);
        $this->assertIsString($result['token']);
        $this->assertEquals($this->user->id, $result['user']->id);
    }

    /** 無効なメールアドレスでのログインテスト */
    public function test_login_with_invalid_email(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'email' => 'invalid@example.com',
            'password' => $this->password
        ]);

        $this->expectException(ValidationException::class);

        ($this->action)($request);
    }

    /** 無効なパスワードでのログインテスト */
    public function test_login_with_invalid_password(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $this->expectException(ValidationException::class);

        ($this->action)($request);
    }

    /** 返されるユーザー情報が正しいテスト */
    public function test_returns_correct_user_information(): void
    {
        $request = new LoginRequest();
        $request->merge([
            'email' => 'test@example.com',
            'password' => $this->password
        ]);

        $result = ($this->action)($request);

        $returnedUser = $result['user'];
        $this->assertEquals($this->user->id, $returnedUser->id);
        $this->assertEquals($this->user->name, $returnedUser->name);
        $this->assertEquals($this->user->email, $returnedUser->email);
    }
} 