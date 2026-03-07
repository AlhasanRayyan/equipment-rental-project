@if($equipments->isEmpty())
    <div class="uk-alert-warning" uk-alert>
        <p class="uk-text-center">لم تقم بإضافة معدات بعد.</p>
    </div>
@else
    <div class="uk-overflow-auto">
        <table class="uk-table uk-table-middle uk-table-divider uk-table-striped uk-table-hover">
            <thead>
                <tr>
                    <th class="uk-table-shrink">#</th>
                    <th class="uk-table-expand">المعدة</th>
                    <th>الفئة</th>
                    <th>السعر اليومي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipments as $item)
                    <tr> 
                        <td>{{ $item->id }}</td>
                        <td class="uk-text-bold uk-text-primary">
                            {{ $item->name }}
                        </td>
                        <td>
                            {{-- الوصول لاسم الفئة من العلاقة --}}
                            <span class="uk-text-muted">{{ $item->category->name ?? 'غير مصنف' }}</span>
                        </td>
                        <td>
                            {{-- التأكد من اسم العمود daily_rate --}}
                            <span class="uk-label uk-label-primary">{{ number_format($item->daily_rate, 2) }} $</span>
                        </td>
                        <td>
                            @if($item->status == 'available')
                                <span class="uk-label uk-label-success">متاح</span>
                            @elseif($item->status == 'rented')
                                <span class="uk-label uk-label-warning">مؤجر</span>
                            @elseif($item->status == 'maintenance')
                                <span class="uk-label uk-label-danger">صيانة</span>
                            @else
                                <span class="uk-label">غير متاح</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif