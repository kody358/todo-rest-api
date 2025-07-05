<?php

namespace Tests\Unit\Actions;

use App\Actions\LogoutAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutActionTest extends TestCase
{
    use RefreshDatabase;

    private LogoutAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new LogoutAction();
        $this->user = User::factory()->create();
    }

    /** 正常なログアウトのテスト */
    public function test_successful_logout(): void
    {
        // トークンを作成
        $token = $this->user->createToken('auth_token');
        
        $request = Request::create('/logout', 'POST');
        $request->setUserResolver(fn() => $this->user);
        
        // 現在のアクセストークンを設定
        $personalAccessToken = PersonalAccessToken::findToken($token->plainTextToken);
        $this->user->withAccessToken($personalAccessToken);
        $request->setUserResolver(fn() => $this->user);

        $result = ($this->action)($request);

        $this->assertTrue($result);
    }

    /** 現在のトークンが削除されるテスト */
    public function test_deletes_current_token(): void
    {
        // トークンを作成
        $token = $this->user->createToken('auth_token');
        $personalAccessToken = PersonalAccessToken::findToken($token->plainTextToken);
        
        // トークンが存在することを確認
        $this->assertNotNull($personalAccessToken);
        
        $request = Request::create('/logout', 'POST');
        $this->user->withAccessToken($personalAccessToken);
        $request->setUserResolver(fn() => $this->user);

        ($this->action)($request);

        // トークンが削除されていることを確認
        $this->assertNull(PersonalAccessToken::findToken($token->plainTextToken));
    }
} 