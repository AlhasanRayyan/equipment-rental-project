<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Carbon\Carbon; // لإدارة التواريخ

class Notification extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'reference_id',
        'is_read',
        'expires_at',
        // 'timestamp' is covered by created_at
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'expires_at' => 'datetime',
        'type' => 'string', // Enum in DB, treated as string
        // 'timestamp' is implicitly covered by 'created_at'
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the notification has been read.
     * +isRead() bool
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Determine if the notification has expired.
     * +isExpired() bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark the notification as read.
     * +markAsRead() void
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->save();
        }
    }

    // Scopes

    /**
     * Scope a query to only include unread notifications.
     * +scopeUnread() QueryBuilder
     */
    public function scopeUnread(Builder $query): void
    {
        $query->where('is_read', false);
    }

    /**
     * Scope a query to only include notifications that have not expired.
     * +scopeNotExpired() QueryBuilder
     */
    public function scopeNotExpired(Builder $query): void
    {
        $query->whereNull('expires_at')->orWhere('expires_at', '>', Carbon::now());
    }

    /**
     * Scope a query to include notifications of a specific type.
     * +scopeByType(type) QueryBuilder
     */
    public function scopeByType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }

    // Relationships

    /**
     * Get the user that this notification belongs to.
     * User "1" --> "0..*" Notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}