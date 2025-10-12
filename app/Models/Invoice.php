<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder; // لاستخدام الـ scope
use Carbon\Carbon; // لإدارة التواريخ

class Invoice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices'; // تأكد من اسم الجدول

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'total_amount',
        'status',
        'pdf_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'status' => 'string', // Enum in DB, treated as string
    ];

    // Accessors and Methods (من الـ classDiagram)

    /**
     * Determine if the invoice has been issued.
     * +isIssued() bool
     */
    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    /**
     * Determine if the invoice has been paid.
     * +isPaid() bool
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Determine if the invoice is overdue.
     * +isOverdue() bool
     */
    public function isOverdue(): bool
    {
        return $this->status === 'issued' && $this->due_date && $this->due_date->isPast();
    }

    /**
     * Determine if the invoice is cancelled.
     * +isCancelled() bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Generate the PDF for the invoice. (Placeholder)
     * +generatePDF() string
     */
    public function generatePDF(): string
    {
        // In a real application, this would involve a PDF generation library (e.g., Dompdf, Snappy)
        // It would generate the PDF, save it to storage (e.g., S3 or local disk),
        // and update the `pdf_url` attribute.
        // For now, returning a dummy URL or string.
        $pdfPath = 'invoices/' . $this->invoice_number . '.pdf';
        // Logic to create PDF content and save it to storage
        // Example: Storage::disk('public')->put($pdfPath, $pdfContent);
        $this->update(['pdf_url' => url('storage/' . $pdfPath)]);
        return $this->pdf_url;
    }

    /**
     * Mark the invoice as paid.
     * +markAsPaid() void
     */
    public function markAsPaid(): void
    {
        if ($this->isIssued() || $this->isOverdue()) {
            $this->status = 'paid';
            $this->save();
        }
    }

    /**
     * Calculate the tax amount for the invoice. (Example logic)
     * +calculateTax() decimal
     */
    public function calculateTax(): float
    {
        // This method assumes 'tax_amount' is already calculated and stored.
        // If it needs to be calculated dynamically, you might fetch a tax rate from AdminSetting.
        // Example: $taxRate = AdminSetting::where('setting_key', 'tax_rate')->value('setting_value');
        // return $this->subtotal * ($taxRate / 100);
        return $this->tax_amount;
    }

    /**
     * Generate a unique invoice number. (Example logic)
     * +generateInvoiceNumber() string
     * This would typically be called before saving a new invoice.
     */
    public function generateInvoiceNumber(): string
    {
        // Example logic: Prefix + Year + Month + Auto-incrementing ID
        // Or using a package like Ramsey\Uuid for UUIDs.
        $prefix = 'INV-';
        $yearMonth = Carbon::now()->format('Ym');
        $lastInvoice = static::where('invoice_number', 'like', $prefix . $yearMonth . '%')
                             ->orderBy('invoice_number', 'desc')
                             ->first();

        $lastNumber = 0;
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4); // Assuming last 4 digits are sequential
        }
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $yearMonth . $newNumber;
    }

    // Scopes

    /**
     * Scope a query to only include overdue invoices.
     * +scopeOverdue() QueryBuilder
     */
    public function scopeOverdue(Builder $query): void
    {
        $query->where('status', 'issued')
              ->whereNotNull('due_date')
              ->where('due_date', '<', Carbon::today());
    }

    // Relationships

    /**
     * Get the booking that this invoice belongs to.
     * Booking "1" --> "1" Invoice
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}