<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     * This will be overridden by states or explicit attributes from the seeder.
     */
    public function definition(): array
    {
        // كحل افتراضي، سننشئ Booking جديداً. هذا سيتم تجاوزه (override) بواسطة Seeder
        // عندما يحدد Booking_id صراحةً. هذا يضمن أن Factory يمكنه العمل بشكل مستقل.
        $booking = Booking::factory()->createQuietly(); // createQuietly لتجنب تشغيل الـ afterCreating callbacks فوراً

        $subtotal = $booking->total_cost;
        $taxRate = $this->faker->randomFloat(2, 0.05, 0.20); // 5-20% tax
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        $issueDate = Carbon::parse($this->faker->dateTimeBetween($booking->created_at, 'now'));
        $dueDate = (clone $issueDate)->addDays($this->faker->numberBetween(7, 30));

        return [
            'booking_id' => $booking->id,
            'invoice_number' => $this->generateUniqueInvoiceNumber(),
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => $this->faker->randomElement(['issued', 'paid', 'overdue', 'cancelled']),
            'pdf_url' => $this->faker->optional()->url(),
        ];
    }

    protected function generateUniqueInvoiceNumber(): string
    {
        do {
            $prefix = 'INV-';
            $yearMonth = Carbon::now()->format('Ym');
            $randomString = strtoupper(Str::random(4));
            $invoiceNumber = $prefix . $yearMonth . '-' . $randomString;
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());
        return $invoiceNumber;
    }

    /**
     * Configure the invoice attributes for a given booking.
     * Use this state when you explicitly know which booking the invoice is for.
     */
    public function forBooking(Booking $booking): static
    {
        // حساب القيم الديناميكية بناءً على الـ Booking المحدد
        $subtotal = $booking->total_cost;
        $taxRate = 0.15; // نسبة ضريبة ثابتة لأغراض الـ seeding
        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount;

        $issueDate = Carbon::parse($this->faker->dateTimeBetween($booking->created_at, 'now'));
        $dueDate = (clone $issueDate)->addDays($this->faker->numberBetween(7, 30));

        return $this->state(fn (array $attributes) => [
            'booking_id' => $booking->id,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
    }

    // دوال States الأخرى (issued, paid, overdue, cancelled) تبقى كما هي
    public function issued(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'issued']);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'paid', 'pdf_url' => $this->faker->url()]);
    }

    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            $issueDate = Carbon::parse($this->faker->dateTimeBetween('-2 months', '-1 month'));
            $dueDate = (clone $issueDate)->subDays($this->faker->numberBetween(1, 15));
            return ['status' => 'overdue', 'issue_date' => $issueDate, 'due_date' => $dueDate];
        });
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'cancelled']);
    }
}