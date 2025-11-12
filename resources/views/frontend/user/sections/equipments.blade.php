@if($equipments->isEmpty())
    <p class="uk-text-center">لم تقم بإضافة معدات بعد.</p>
@else
<table class="uk-table uk-table-middle uk-table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>المعدة</th>
            <th>الفئة</th>
            <th>السعر اليومي</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($equipments as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->daily_price }} شيكل</td>
                <td>
                    @if($item->status == 'available')
                        <span class="uk-label uk-label-success">متاح</span>
                    @else
                        <span class="uk-label uk-label-warning">غير متاح</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
