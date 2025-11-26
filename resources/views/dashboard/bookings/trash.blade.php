@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">سلة محذوفات الحجوزات</h1>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> رجوع للحجوزات
            </a>
        </div>

        @include('partials.alerts')

        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-trash-alt me-2"></i>الحجوزات المحذوفة ({{ $bookings->total() }})
                </h6>

                @if ($bookings->total() > 0)
                    <div>
                        <form action="{{ route('admin.bookings.restoreAll') }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-sm btn-success" type="submit">
                                <i class="fas fa-undo"></i> استرجاع الكل
                            </button>
                        </form>

                        <form action="{{ route('admin.bookings.forceDeleteAll') }}" method="POST" class="d-inline ms-1"
                            onsubmit="return confirm('حذف نهائي لكل الحجوزات؟ لا يمكن التراجع.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">
                                <i class="fas fa-times"></i> حذف الكل نهائياً
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>المعدة</th>
                                <th>المستأجر</th>
                                <th>الحالة</th>
                                <th>تاريخ الحذف</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->equipment->name ?? '-' }}</td>
                                    <td>{{ $booking->renter->first_name ?? '-' }} {{ $booking->renter->last_name ?? '' }}
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $booking->booking_status }}</span></td>
                                    <td>{{ $booking->deleted_at?->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.bookings.restore', $booking->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit">
                                                استرجاع
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.bookings.forceDelete', $booking->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('حذف نهائي لهذا الحجز؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">
                                                حذف نهائي
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">
                                        لا توجد حجوزات في سلة المحذوفات.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3 px-3 pb-3">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
