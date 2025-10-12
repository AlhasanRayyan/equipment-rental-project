<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Carbon\Carbon; // لإدارة التواريخ (إذا لزم الأمر للدوال)

class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'amount',
        'payment_date', // هذا الحقل تم تمثيله بـ created_at في الـ migration
        'payment_method',
        'transaction_id',
        'status',
        'payment_type',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_method' => 'string', // Enum in DB, treated as string
        'status' => 'string',         // Enum in DB, treated as string
        'payment_type' => 'string',   // Enum in DB, treated as string
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the payment was successful.
     * +isSuccessful() bool
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if the payment failed.
     * +isFailed() bool
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Determine if the payment is still pending.
     * +isPending() bool
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Determine if the payment was refunded.
     * +isRefunded() bool
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Process the payment. (Placeholder for integration logic)
     * +process() bool
     */
    public function process(): bool
    {

        // Simulating success for now:
        $this->status = 'completed';
        $this->transaction_id = 'TRX-' . uniqid(); // Generate a dummy transaction ID
        $this->save();
        return true;
    }

    /**
     * Refund the payment. (Placeholder for integration logic)
     * +refund() bool
     */
    public function refund(): bool
    {
        if ($this->isSuccessful() && !$this->isRefunded()) {

            // Simulating refund success for now:
            $this->status = 'refunded';
            $this->save();

            // Create a new refund payment entry for tracking
            Payment::create([
                'booking_id' => $this->booking_id,
                'user_id' => $this->user_id,
                'amount' => $this->amount, // Amount of refund
                'payment_method' => $this->payment_method,
                'transaction_id' => 'REF-' . $this->transaction_id,
                'status' => 'completed',
                'payment_type' => 'refund',
                'notes' => 'Refund for original payment ' . $this->id,
            ]);

            return true;
        }
        return false;
    }

    // Scopes

    /**
     * Scope a query to only include successful payments.
     * +scopeSuccessful() QueryBuilder
     */
    public function scopeSuccessful(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    /**
     * Scope a query to include payments by a specific method.
     * +scopeByMethod(method) QueryBuilder
     */
    public function scopeByMethod(Builder $query, string $method): void
    {
        $query->where('payment_method', $method);
    }

    // Relationships

    /**
     * Get the booking that this payment belongs to.
     * Booking "1" --> "0..*" Payment
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * Get the user who made this payment.
     * User "1" --> "0..*" Payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
