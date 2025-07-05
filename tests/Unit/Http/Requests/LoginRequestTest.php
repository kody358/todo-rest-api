<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    private LoginRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new LoginRequest();
    }

    /** バリデーションルールのテスト */
    public function test_validation_rules(): void
    {
        $expectedRules = [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];

        $this->assertEquals($expectedRules, $this->request->rules());
    }

    /** 認可のテスト */
    public function test_authorize_returns_true(): void
    {
        $this->assertTrue($this->request->authorize());
    }

    /** メールアドレス必須のテスト */
    public function test_email_is_required(): void
    {
        $data = ['password' => 'password123'];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertEquals('メールアドレスは必須項目です', $validator->errors()->first('email'));
    }

    /** メールアドレス形式のテスト */
    public function test_email_must_be_valid_format(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123'
        ];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertEquals('有効なメールアドレスを入力してください', $validator->errors()->first('email'));
    }

    /** パスワード必須のテスト */
    public function test_password_is_required(): void
    {
        $data = ['email' => 'test@example.com'];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
        $this->assertEquals('パスワードは必須項目です', $validator->errors()->first('password'));
    }

    /** パスワード文字列型のテスト */
    public function test_password_must_be_string(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 12345678
        ];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertEquals('パスワードは文字列で入力してください', $validator->errors()->first('password'));
    }

    /** パスワード最小文字数のテスト */
    public function test_password_minimum_length(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => '1234567' // 7文字
        ];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertEquals('パスワードは8文字以上で入力してください', $validator->errors()->first('password'));
    }

    /** 正常なデータのテスト */
    public function test_passes_with_valid_data(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }

    /** 様々なメールアドレス形式のテスト */
    public function test_accepts_various_email_formats(): void
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.jp',
            'admin+test@example.org',
            'user123@sub.domain.com'
        ];

        foreach ($validEmails as $email) {
            $data = [
                'email' => $email,
                'password' => 'password123'
            ];
            $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

            $this->assertTrue($validator->passes(), "Email {$email} should be valid");
        }
    }

    /** 無効なメールアドレス形式のテスト */
    public function test_rejects_invalid_email_formats(): void
    {
        $invalidEmails = [
            'plainaddress',
            '@missingdomain.com',
            'missing@.com',
            'missing.domain@.com',
            'spaces in@email.com'
        ];

        foreach ($invalidEmails as $email) {
            $data = [
                'email' => $email,
                'password' => 'password123'
            ];
            $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

            $this->assertTrue($validator->fails(), "Email {$email} should be invalid");
        }
    }

    /** パスワード境界値のテスト */
    public function test_password_boundary_values(): void
    {
        // 8文字ちょうど（通るべき）
        $data8chars = [
            'email' => 'test@example.com',
            'password' => '12345678'
        ];
        $validator8chars = Validator::make($data8chars, $this->request->rules(), $this->request->messages());
        $this->assertTrue($validator8chars->passes());

        // 7文字（失敗すべき）
        $data7chars = [
            'email' => 'test@example.com',
            'password' => '1234567'
        ];
        $validator7chars = Validator::make($data7chars, $this->request->rules(), $this->request->messages());
        $this->assertTrue($validator7chars->fails());
    }
} 