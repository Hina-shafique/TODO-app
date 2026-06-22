<?php

namespace App\Models;

use App\Enum\TodoPriority;
use App\Enum\TodoStatus;
use Database\Factories\TodoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property TodoStatus $status
 * @property TodoPriority $priority
 * @property Carbon|null $due_date
 */
class Todo extends Model
{
    /** @use HasFactory<TodoFactory> */
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

    public function bookmarkedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }
}
