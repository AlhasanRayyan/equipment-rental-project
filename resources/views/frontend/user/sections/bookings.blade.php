@if($bookings->isEmpty())
    <p class="uk-text-center">لا توجد حجوزات حالياً.</p>
@else
<table class="uk-table uk-table-divider">
    <thead>
        <tr>
            <th>#</th>
            <th>المعدة</th>
            <th>من</th>
            <th>إلى</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $b)
            <tr>
                <td>{{ $b->id }}</td>
                <td>{{ $b->equipment->name ?? 'تم حذف المعدة' }}</td>
                <td>{{ $b->start_date }}</td>
                <td>{{ $b->end_date }}</td>
                <td>
                    <span class="uk-label {{ $b->status === 'confirmed' ? 'uk-label-success' : ($b->status === 'cancelled' ? 'uk-label-danger' : 'uk-label-warning') }}">
                        {{ ucfirst($b->status) }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
