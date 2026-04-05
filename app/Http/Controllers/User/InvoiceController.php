<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice)
    {
        if ($invoice->booking->renter_id !== auth()->id()) {
            abort(403);
        }

        $invoice->load([
            'booking.equipment',
            'booking.renter',
            'booking.owner',
        ]);

        $mpdf = new Mpdf([
            'mode'    => 'utf-8',
            'format'  => 'A4',
            'direction' => 'rtl',
        ]);

        $html = view('dashboard.invoices.pdf', compact('invoice'))->render();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }
}