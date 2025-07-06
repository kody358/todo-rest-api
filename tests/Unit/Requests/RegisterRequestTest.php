<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getValidationRules(): array
    {
        return (new RegisterRequest())->rules();
    }

    private function getValidationMessages(): array
    {
        return (new RegisterRequest())->messages();
    }

    /** 有効なデータでのバリデーションテスト */
    public function test_validation_passes_with_valid_data(): void
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->passes());
    }

    /** 名前が必須であることのテスト */
    public function test_name_is_required(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** 名前が文字列であることのテスト */
    public function test_name_must_be_string(): void
    {
        $data = [
            'name' => 123,
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** 名前の最大文字数制限テスト */
    public function test_name_max_length(): void
    {
        $data = [
            'name' => str_repeat('あ', 256), // 256文字
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    /** メールアドレスが必須であることのテスト */
    public function test_email_is_required(): void
    {
        $data = [
            'name' => 'テストユーザー',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** メールアドレス形式のテスト */
    public function test_email_must_be_valid_format(): void
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** メールアドレスの重複チェックテスト */
    public function test_email_must_be_unique(): void
    {
        // 既存ユーザーを作成
        User::factory()->create(['email' => 'existing@example.com']);

        $data = [
            'name' => 'テストユーザー',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /** パスワードが必須であることのテスト */
    public function test_password_is_required(): void
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password_confirmation' => 'password123',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** パスワードの最小文字数テスト */
    public function test_password_minimum_length(): void
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567', // 7文字
            'password_confirmation' => '1234567',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /** パスワード確認が一致することのテスト */
    public function test_password_confirmation_must_match(): void
    {
        $data = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ];

        $validator = Validator::make($data, $this->getValidationRules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }
} 