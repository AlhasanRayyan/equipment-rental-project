<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Carbon\Carbon; // لإدارة التواريخ

class Review extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reviews'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'reviewer_id',
        'reviewed_user_id',
        'equipment_id',
        'rating_overall',
        'comment',
        // 'review_date' is covered by created_at
        'owner_communication_rating',
        'equipment_condition_rating',
        'renter_punctuality_rating',
        'is_verified',
        'owner_response',
        'owner_response_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating_overall' => 'integer',
        'owner_communication_rating' => 'integer',
        'equipment_condition_rating' => 'integer',
        'renter_punctuality_rating' => 'integer',
        'is_verified' => 'boolean',
        'owner_response_date' => 'datetime',
        // 'review_date' is implicitly covered by 'created_at'
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the overall rating is considered positive (e.g., 4 or 5 stars).
     * +isPositive() bool
     */
    public function isPositive(): bool
    {
        return $this->rating_overall >= 4; // Assuming a 1-5 scale
    }

    /**
     * Determine if an owner response has been provided.
     * +hasResponse() bool
     */
    public function hasResponse(): bool
    {
        return !is_null($this->owner_response) && !empty($this->owner_response);
    }

    /**
     * Add an owner's response to the review.
     * +addResponse(response) void
     */
    public function addResponse(string $response): void
    {
        $this->owner_response = $response;
        $this->owner_response_date = Carbon::now();
        $this->save();
    }

    /**
     * Mark the review as verified.
     * +verify() void
     */
    public function verify(): void
    {
        $this->is_verified = true;
        $this->save();
    }

    // Scopes

    /**
     * Scope a query to only include verified reviews.
     * +scopeVerified() QueryBuilder
     */
    public function scopeVerified(Builder $query): void
    {
        $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include reviews with a minimum overall rating.
     * +scopeByRating(min_rating) QueryBuilder
     */
    public function scopeByRating(Builder $query, int $minRating): void
    {
        $query->where('rating_overall', '>=', $minRating);
    }

    // Relationships

    /**
     * Get the booking that this review is associated with.
     * Booking "1" --> "0..1" Review
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the user who wrote this review.
     * User "1" --> "0..*" Review : writes
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get the user who was reviewed.
     * User "1" --> "0..*" Review : is_reviewed_in
     */
    public function reviewedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_user_id');
    }

    /**
     * Get the equipment that was reviewed.
     * Equipment "1" --> "0..*" Review
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}