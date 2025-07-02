<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'completed',
        'user_id',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Todoが属するユーザーを返す
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 完了済みのタスクを返す
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * 未完了のタスクを返す
     */
    public function scopePending($query)
    {
        return $query->where('completed', false);
    }
}
