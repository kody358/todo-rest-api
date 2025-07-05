<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\UpdateTodoRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateTodoRequestTest extends TestCase
{
    private UpdateTodoRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new UpdateTodoRequest();
    }

    /** バリデーションルールのテスト */
    public function test_validation_rules(): void
    {
        $expectedRules = [
            'title' => 'sometimes|required',
            'content' => 'nullable',
            'completed' => 'sometimes|boolean',
        ];

        $this->assertEquals($expectedRules, $this->request->rules());
    }

    /** 認可のテスト */
    public function test_authorize_returns_true(): void
    {
        $this->assertTrue($this->request->authorize());
    }


    /** 内容はnullableのテスト */
    public function test_content_is_nullable(): void
    {
        $data = ['title' => 'テスト'];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }

    /** completed真偽値のテスト */
    public function test_completed_must_be_boolean(): void
    {
        $data = ['completed' => 'true'];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->fails());
        $this->assertEquals('完了状態は真偽値で入力してください', $validator->errors()->first('completed'));
    }

    /** completed正常値のテスト */
    public function test_completed_accepts_boolean_values(): void
    {
        $dataTrue = ['completed' => true];
        $validatorTrue = Validator::make($dataTrue, $this->request->rules(), $this->request->messages());
        $this->assertTrue($validatorTrue->passes());

        $dataFalse = ['completed' => false];
        $validatorFalse = Validator::make($dataFalse, $this->request->rules(), $this->request->messages());
        $this->assertTrue($validatorFalse->passes());
    }

    /** 正常なデータのテスト */
    public function test_passes_with_valid_data(): void
    {
        $data = [
            'title' => 'テストタイトル',
            'content' => 'テスト内容',
            'completed' => true
        ];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }

    /** 部分的なデータでも通るテスト */
    public function test_passes_with_partial_data(): void
    {
        $data = ['completed' => false];
        $validator = Validator::make($data, $this->request->rules(), $this->request->messages());

        $this->assertTrue($validator->passes());
    }
} 