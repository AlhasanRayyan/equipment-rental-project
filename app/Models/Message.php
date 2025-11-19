<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Carbon\Carbon; // لإدارة التواريخ (إذا لزم الأمر للدوال)
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory,  SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'booking_id',
        'content',
        'message_type',
        'attachment_url',
        // 'timestamp' is covered by created_at
        'is_read',
        'is_resolved', // **تأكد أن هذا موجود هنا**

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'message_type' => 'string', // Enum in DB, treated as string
        'is_resolved' => 'boolean', // **تأكد أن هذا موجود هنا**
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the message has been read.
     * +isRead() bool
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Mark the message as read.
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
     * Scope a query to only include unread messages.
     * +scopeUnread() QueryBuilder
     */
    public function scopeUnread(Builder $query): void
    {
        $query->where('is_read', false);
    }

    /**
     * Scope a query to only include messages related to a specific booking.
     * +scopeBookingRelated() QueryBuilder
     */
    public function scopeBookingRelated(Builder $query): void
    {
        $query->whereNotNull('booking_id');
    }

    /**
     * Scope a query to include messages between two specific users (a conversation).
     * +scopeConversation(user1, user2) QueryBuilder
     */
    public function scopeConversation(Builder $query, int $user1Id, int $user2Id): void
    {
        $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where(function ($q1) use ($user1Id, $user2Id) {
                $q1->where('sender_id', $user1Id)
                    ->where('receiver_id', $user2Id);
            })->orWhere(function ($q2) use ($user1Id, $user2Id) {
                $q2->where('sender_id', $user2Id)
                    ->where('receiver_id', $user1Id);
            });
        })->orderBy('created_at'); // Order by timestamp to show conversation chronologically
    }

    // Relationships

    /**
     * Get the user who sent this message.
     * User "1" --> "0..*" Message : sends
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received this message.
     * User "1" --> "0..*" Message : receives
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the booking that this message is related to (if any).
     * Booking "1" --> "0..*" Message
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
