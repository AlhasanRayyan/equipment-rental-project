@if($rented->isEmpty())
    <p class="uk-text-center">لا توجد معدات مستأجرة حالياً.</p>
@else
<table class="uk-table uk-table-small uk-table-divider">
    <thead>
        <tr>
            <th>#</th>
            <th>المعدة</th>
            <th>الفترة</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rented as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->equipment->name ?? 'تم حذف المعدة' }}</td>
                <td>{{ $r->start_date }} - {{ $r->end_date }}</td>
                <td>{{ ucfirst($r->status) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
