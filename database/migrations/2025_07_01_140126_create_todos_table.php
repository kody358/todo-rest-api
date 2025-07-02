<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('ユーザーID');
            $table->string('title')->comment('タイトル（必須）');
            $table->text('content')->nullable()->comment('内容（任意）');
            $table->boolean('completed')->default(false)->comment('完了状態');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};
