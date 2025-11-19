<?php

namespace App\Models;

use App\Models\User;
use App\Models\Equipment;
use Carbon\Carbon; // لإدارة التواريخ
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipment_id',
        'renter_id',
        'owner_id',
        'start_date',
        'end_date',
        'rental_duration_days',
        'rental_rate_type',
        'total_cost',
        'deposit_amount_paid',
        'payment_status',
        'booking_status',
        'pickup_location',
        'return_location',
        'contract_url',
        'special_requirements',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rental_duration_days' => 'integer',
        'total_cost' => 'decimal:2',
        'deposit_amount_paid' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'rental_rate_type' => 'string', // Enum in DB, treated as string
        'payment_status' => 'string',   // Enum in DB, treated as string
        'booking_status' => 'string',   // Enum in DB, treated as string
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the booking is in a pending state.
     * +isPending() bool
     */
    public function isPending(): bool
    {
        return $this->booking_status === 'pending';
    }

    /**
     * Determine if the booking is confirmed.
     * +isConfirmed() bool
     */
    public function isConfirmed(): bool
    {
        return $this->booking_status === 'confirmed';
    }

    /**
     * Determine if the booking is currently active (between start_date and end_date).
     * +isActive() bool
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->booking_status === 'active' &&
            $this->start_date->lte($now) &&
            $this->end_date->gte($now);
    }

    /**
     * Determine if the booking is completed.
     * +isCompleted() bool
     */
    public function isCompleted(): bool
    {
        return $this->booking_status === 'completed';
    }

    /**
     * Determine if the booking is cancelled.
     * +isCancelled() bool
     */
    public function isCancelled(): bool
    {
        return $this->booking_status === 'cancelled';
    }

    /**
     * Determine if the booking can still be cancelled.
     * (Example logic: can be cancelled if pending or confirmed, and start date is in the future)
     * +canBeCancelled() bool
     */
    public function canBeCancelled(): bool
    {
        return ($this->isPending() || $this->isConfirmed()) && $this->start_date->isFuture();
    }

    /**
     * Determine if a review can be submitted for this booking.
     * (Example logic: booking is completed and no review yet)
     * +canBeReviewed() bool
     */
    public function canBeReviewed(): bool
    {
        return $this->isCompleted() && !$this->review()->exists();
    }

    /**
     * Confirm the booking.
     * +confirm() void
     */
    public function confirm(): void
    {
        if ($this->isPending()) {
            $this->booking_status = 'confirmed';
            $this->confirmed_at = Carbon::now();
            $this->save();

            // Optionally, update equipment status
            $this->equipment->markAsRented(); // This method is defined in Equipment model
        }
    }

    /**
     * Cancel the booking.
     * +cancel(reason) void
     */
    public function cancel(string $reason = null): void
    {
        if ($this->canBeCancelled()) {
            $this->booking_status = 'cancelled';
            $this->cancellation_reason = $reason;
            $this->cancelled_at = Carbon::now();
            $this->save();

            // Optionally, update equipment status if it was set to rented
            if ($this->equipment->status === 'rented') {
                $this->equipment->markAsAvailable(); // This method is defined in Equipment model
            }
        }
    }

    /**
     * Mark the booking as completed.
     * +complete() void
     */
    public function complete(): void
    {
        if ($this->booking_status === 'active' || ($this->isConfirmed() && $this->end_date->isPast())) {
            $this->booking_status = 'completed';
            $this->save();

            // Optionally, update equipment status
            if ($this->equipment->status === 'rented') {
                $this->equipment->markAsAvailable(); // This method is defined in Equipment model
            }
        }
    }

    /**
     * Calculate the remaining days until the end of the rental.
     * +calculateRemainingDays() int
     */
    public function calculateRemainingDays(): int
    {
        if ($this->isCompleted() || $this->isCancelled()) {
            return 0;
        }
        $today = Carbon::today();
        if ($this->end_date->isFuture()) {
            return $this->end_date->diffInDays($today);
        }
        return 0;
    }

    /**
     * Generate a contract URL or content. (Placeholder for now)
     * +generateContract() string
     *
     * In a real application, this would likely generate a PDF and store its URL.
     */
    public function generateContract(): string
    {
        // Example: logic to generate a PDF contract and store it, then return its URL.
        // For now, it's a placeholder.
        $contractContent = "Rental Contract for Booking ID: {$this->id}\n";
        $contractContent .= "Equipment: {$this->equipment->name}\n";
        $contractContent .= "Renter: {$this->renter->first_name} {$this->renter->last_name}\n";
        $contractContent .= "Owner: {$this->owner->first_name} {$this->owner->last_name}\n";
        $contractContent .= "From {$this->start_date->format('Y-m-d')} to {$this->end_date->format('Y-m-d')}\n";
        $contractContent .= "Total Cost: {$this->total_cost}\n";
        return $this->contract_url ?? "No contract generated yet. Content: \n" . $contractContent;
    }

    // Scopes

    /**
     * Scope a query to only include active bookings.
     * +scopeActive() QueryBuilder
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('booking_status', 'active');
    }

    /**
     * Scope a query to only include completed bookings.
     * +scopeCompleted() QueryBuilder
     */
    public function scopeCompleted(Builder $query): void
    {
        $query->where('booking_status', 'completed');
    }

    /**
     * Scope a query to include bookings within a specified date range.
     * +scopeByDateRange(start, end) QueryBuilder
     */
    public function scopeByDateRange(Builder $query, Carbon $startDate, Carbon $endDate): void
    {
        $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        });
    }

    // Relationships

    /**
     * Get the equipment for this booking.
     * Equipment "1" --> "0..*" Booking
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    /**
     * Get the renter user for this booking.
     * User "1" --> "0..*" Booking : as_renter
     */
    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    /**
     * Get the owner user of the equipment for this booking.
     * User "1" --> "0..*" Booking : as_owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the payments associated with this booking.
     * Booking "1" --> "0..*" Payment
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    /**
     * Get the invoice for this booking.
     * Booking "1" --> "1" Invoice
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'booking_id');
    }

    /**
     * Get the review for this booking.
     * Booking "1" --> "0..1" Review
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'booking_id');
    }

    /**
     * Get the messages associated with this booking.
     * Booking "1" --> "0..*" Message
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'booking_id');
    }
}
