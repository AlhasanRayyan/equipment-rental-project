<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with([
            'booking',
            'booking.equipment',
            'booking.renter',
            'booking.owner',
        ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($bookingQuery) use ($search) {
                        $bookingQuery->where('id', $search)
                            ->orWhereHas('renter', function ($renterQuery) use ($search) {
                                $renterQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                                $ownerQuery->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->orWhereHas('equipment', function ($equipmentQuery) use ($search) {
                                $equipmentQuery->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        $invoices = $query->latest()->paginate(10);

        $stats = [
            'total' => Invoice::count(),
            'issued' => Invoice::where('status', 'issued')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'cancelled' => Invoice::where('status', 'cancelled')->count(),
        ];

        return view('dashboard.invoices.index', compact('invoices', 'stats'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'booking',
            'booking.equipment',
            'booking.renter',
            'booking.owner',
        ]);

        return view('dashboard.invoices.show', compact('invoice'));
    }

    public function download(Invoice $invoice)
    {
        $invoice->load([
            'booking',
            'booking.equipment',
            'booking.renter',
            'booking.owner',
        ]);

        $pdf = Pdf::loadView('dashboard.invoices.pdf', compact('invoice'));

        return $pdf->download($invoice->invoice_number . '.pdf');
    }
}
