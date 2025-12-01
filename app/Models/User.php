<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'role',
        'profile_picture_url',
        'description',
        'location_text',
        'is_active',
        'last_login',
        'average_owner_rating',
        'average_renter_rating',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'average_owner_rating' => 'decimal:2',
        'average_renter_rating' => 'decimal:2',
        'role' => 'string',
    ];

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get the user's combined average rating.
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: function () {
                $ownerRating = $this->average_owner_rating;
                $renterRating = $this->average_renter_rating;

                if ($ownerRating > 0 && $renterRating > 0) {
                    return ($ownerRating + $renterRating) / 2;
                } elseif ($ownerRating > 0) {
                    return $ownerRating;
                } elseif ($renterRating > 0) {
                    return $renterRating;
                }
                return 0.00;
            },
        );
    }

    // Relationships
    public function ownedEquipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'owner_id');
    }

    public function rentedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'renter_id');
    }

    public function ownedBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'owner_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function writtenReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function receivedReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }

    // علاقات جديدة للمحادثات
    public function initiatedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'renter_id');
    }

    public function receivedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'owner_id');
    }

    // دالة مجمعة للحصول على جميع المحادثات التي يشارك فيها المستخدم
    public function conversations(): HasMany
    {
        // هذه الدالة ستحتاج لـ "HasManyThrough" أو دالة يدوية، لكن للاختصار
        // سنعتمد على جلب المحادثات في الـ controller
        // ولكن يمكن إضافة علاقة مباشرة من الـ User إلى الـ Messages
        return $this->hasMany(Message::class, 'sender_id')
                    ->orWhere('receiver_id', $this->id); // هذه الطريقة ليست مثالية للموديل ولكنها ممكنة
    }

    // العلاقات الأصلية التي قدمتها لموديل الرسائل
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class, 'user_id');
    }

    public function updatedAdminSettings(): HasMany
    {
        return $this->hasMany(AdminSetting::class, 'updated_by');
    }

    // Helper to get full name
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}