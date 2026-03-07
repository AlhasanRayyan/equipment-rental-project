@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">

            <div class="col-md-11 col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">إدارة الفواتير</h2>
                </div>

                <div class="row mb-4">
                    <div class="row mb-4">
                        <div class="col-md-6 col-lg mb-3">
                            <div class="card border-0 shadow-sm border-start border-4 border-secondary">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">إجمالي الفواتير</h6>
                                    <h4 class="mb-0 text-secondary">{{ $stats['total'] }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg mb-3">
                            <div class="card border-0 shadow-sm border-start border-4 border-primary">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">صادرة</h6>
                                    <h4 class="mb-0 text-primary">{{ $stats['issued'] }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg mb-3">
                            <div class="card border-0 shadow-sm border-start border-4 border-success">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">مدفوعة</h6>
                                    <h4 class="mb-0 text-success">{{ $stats['paid'] }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg mb-3">
                            <div class="card border-0 shadow-sm border-start border-4 border-warning">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">متأخرة</h6>
                                    <h4 class="mb-0 text-warning">{{ $stats['overdue'] }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg mb-3">
                            <div class="card border-0 shadow-sm border-start border-4 border-danger">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">ملغية</h6>
                                    <h4 class="mb-0 text-danger">{{ $stats['cancelled'] }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.invoices.index') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">بحث</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="رقم الفاتورة / الحجز / المستأجر / المالك / المعدة"
                                            value="{{ request('search') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">الحالة</label>
                                        <select name="status" class="form-select" style="max-width: 200px"
                                            onchange="this.form.submit()">
                                            <option value="">كل الحالات</option>
                                            <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>
                                                صادرة
                                            </option>
                                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>
                                                مدفوعة
                                            </option>
                                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>
                                                متأخرة
                                            </option>
                                            <option value="cancelled"
                                                {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغية
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">من تاريخ</label>
                                        <input type="date" name="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">إلى تاريخ</label>
                                        <input type="date" name="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>

                                    <div class="col-md-1 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> فلترة
                                        </button>
                                    </div>

                                    <div class="col-md-1 d-flex align-items-end">
                                        <a href="{{ route('admin.invoices.index') }}"
                                            class="btn btn-outline-secondary w-100">
                                            إعادة تعيين
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>رقم الفاتورة</th>
                                            <th>رقم الحجز</th>
                                            <th>المستأجر</th>
                                            <th>المالك</th>
                                            <th>المعدة</th>
                                            <th>تاريخ الإصدار</th>
                                            <th>الإجمالي</th>
                                            <th>الحالة</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoices as $invoice)
                                            <tr>
                                                <td>{{ $invoice->id }}</td>
                                                <td>{{ $invoice->invoice_number }}</td>
                                                <td>{{ $invoice->booking->id ?? '-' }}</td>

                                                <td>
                                                    {{ $invoice->booking->renter->first_name ?? '' }}
                                                    {{ $invoice->booking->renter->last_name ?? '' }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $invoice->booking->renter->email ?? '' }}</small>
                                                </td>

                                                <td>
                                                    {{ $invoice->booking->owner->first_name ?? '' }}
                                                    {{ $invoice->booking->owner->last_name ?? '' }}
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $invoice->booking->owner->email ?? '' }}</small>
                                                </td>

                                                <td>{{ $invoice->booking->equipment->name ?? '-' }}</td>
                                                <td>{{ $invoice->issue_date?->format('Y-m-d') }}</td>
                                                <td>${{ number_format((float) $invoice->total_amount, 2) }}</td>


                                                <td>
                                                    @switch($invoice->status)
                                                        @case('issued')
                                                            <span class="badge bg-primary">صادرة</span>
                                                        @break

                                                        @case('paid')
                                                            <span class="badge bg-success">مدفوعة</span>
                                                        @break

                                                        @case('overdue')
                                                            <span class="badge bg-warning text-dark">متأخرة</span>
                                                        @break

                                                        @case('cancelled')
                                                            <span class="badge bg-danger">ملغية</span>
                                                        @break

                                                        @default
                                                            -
                                                    @endswitch
                                                </td>

                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}"
                                                            class="btn btn-sm btn-outline-info">
                                                            عرض
                                                        </a>
                                                        <a href="{{ route('admin.invoices.download', $invoice->id) }}"
                                                            class="btn btn-sm btn-outline-dark">
                                                            PDF
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-4">لا توجد فواتير حالياً.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-3">
                                    {{ $invoices->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endsection
