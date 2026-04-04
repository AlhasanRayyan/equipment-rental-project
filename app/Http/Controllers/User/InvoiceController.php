<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice)
    {
        // تأكد إن الفاتورة تبع المستخدم الحالي
        if ($invoice->booking->renter_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load([
            'booking.equipment',
            'booking.renter',
            'booking.owner',
        ]);

        $pdf = Pdf::loadView('dashboard.invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_number . '.pdf');
    }
}