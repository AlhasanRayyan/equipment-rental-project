<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    use HasFactory;

    protected $table = 'payment_proofs';

    protected $fillable = [
        'payment_id',
        'booking_id',
        'renter_id',
        'transferred_amount',
        'bank_or_wallet_name',
        'proof_image',
        'notes',
        'review_status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'transferred_amount' => 'decimal:2',
        'reviewed_at'        => 'datetime',
    ];

    // ─── Helpers ────────────────────────────────────────────────

    public function isPending(): bool  { return $this->review_status === 'pending'; }
    public function isApproved(): bool { return $this->review_status === 'approved'; }
    public function isRejected(): bool { return $this->review_status === 'rejected'; }

    public function statusLabel(): string
    {
        return match ($this->review_status) {
            'pending'  => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            default    => 'غير معروف',
        };
    }

    public function statusColor(): string
    {
        return match ($this->review_status) {
            'pending'  => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default    => 'secondary',
        };
    }

    // ─── Relationships ───────────────────────────────────────────

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}