<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'renter_id',
        'equipment_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the other user in the conversation based on the current authenticated user.
     */
    public function getOtherUserAttribute()
    {
        $currentUser = auth()->user();
        if (!$currentUser) {
            return null; // أو رمي خطأ إذا كان يجب أن يكون المستخدم مسجل الدخول
        }

        if ($this->owner_id === $currentUser->id) {
            return $this->renter;
        } elseif ($this->renter_id === $currentUser->id) {
            return $this->owner;
        }
        return null; // لن يحدث هذا إذا كانت المحادثة للمستخدم الحالي
    }
}