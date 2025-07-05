<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreTodoRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTodoRequestTest extends TestCase
{
    private StoreTodoRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreTodoRequest();
    }

    /** バリデーションルールのテスト */
    public function test_validation_rules(): void
    {
        $expectedRules = [
            'title' => 'required',
            'content' => 'nullable',
        ];

        $this->assertEquals($expectedRules, $this->request->rules());
    }

    /** タイトル必須のテスト */
    public function test_title_is_required(): void
    {
        $validator = Validator::make([], $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertEquals('タイトルは必須項目です', $validator->errors()->first('title'));
    }

    /** 内容はnullableのテスト */
    public function test_content_is_nullable(): void
    {
        $data = ['title' => 'テスト'];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }

    /** 正常なデータのテスト */
    public function test_passes_with_valid_data(): void
    {
        $data = [
            'title' => 'テストタイトル',
            'content' => 'テスト内容'
        ];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }

    /** 最小限のデータでも通るテスト */
    public function test_passes_with_minimum_data(): void
    {
        $data = ['title' => 'テスト'];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }
} 