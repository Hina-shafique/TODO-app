<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enum\TodoStatus;
use App\Enum\TodoPriority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property TodoStatus $status
 * @property TodoPriority $priority
 */

class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TodoStatus::class,
            'priority' => TodoPriority::class,
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', TodoStatus::PENDING);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', TodoStatus::COMPLETED);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', today())->where('status', '!=', TodoStatus::COMPLETED);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
