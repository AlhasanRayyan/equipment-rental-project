<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory,  Notifiable, SoftDeletes;

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
        'password' => 'hashed', // Laravel 10+ يقوم بتجزئة (hashing) الباسورد تلقائياً
        'is_active' => 'boolean',
        'last_login' => 'datetime',
        'average_owner_rating' => 'decimal:2',
        'average_renter_rating' => 'decimal:2',
        'role' => 'string', // Enum in DB, but treated as string in application
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
     * هذا Accessor يعتمد على وجود الـ average_owner_rating و average_renter_rating.
     * بناءً على الـ classDiagram، "average_rating" هو دالة تحسب المتوسط.
     * سنفترض أنها متوسط بين تقييم المالك وتقييم المستأجر إذا كان كلاهما موجوداً،
     * أو إحدى القيمتين إذا كانت الأخرى صفر.
     */
    protected function averageRating()
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

    public function ownedEquipment()
    {
        return $this->hasMany(Equipment::class, 'owner_id');
    }


    public function rentedBookings()
    {
        return $this->hasMany(Booking::class, 'renter_id');
    }

    public function ownedBookings()
    {
        return $this->hasMany(Booking::class, 'owner_id');
    }


    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }


    public function writtenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }


    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewed_user_id');
    }


    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }


    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

  /**
     * العناصر المفضلة الخاصة بالمستخدم
     */
    public function favorites()
    {
        return $this->hasMany(UserFavorite::class, 'user_id');
    }


    public function updatedAdminSettings()
    {
        return $this->hasMany(AdminSetting::class, 'updated_by');
    }
    public function equipments()
{
    return $this->hasMany(Equipment::class, 'owner_id');
}
   /**
     * الفواتير الخاصة بالمستخدم
     */
    // public function invoices()
    // {
    //     return $this->hasMany(Invoice::class, 'user_id');
    // }
    public function invoices()
{
    return $this->hasManyThrough(
        Invoice::class,   // المودل النهائي
        Booking::class,   // المودل الوسيط
        'renter_id',      // المفتاح في جدول الحجوزات اللي يربط المستخدم (مثلاً renter_id أو user_id)
        'booking_id',     // المفتاح في جدول الفواتير اللي يربط الحجز
        'id',             // المفتاح الأساسي في جدول المستخدمين
        'id'              // المفتاح الأساسي في جدول الحجوزات
    );
}

      /**
     * جميع الحجوزات التي تخص المستخدم (كمالك أو كمستأجر)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'renter_id');
    }

    //  /**
    //  * المعدات المستأجرة من هذا المستخدم (اختياري)
    //  */
    // public function rentedEquipments()
    // {
    //     // لو عندك عمود renter_id بجدول المعدات
    //     return $this->hasMany(Equipment::class, 'renter_id');
    // }
}
