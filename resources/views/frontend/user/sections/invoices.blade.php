@if($invoices->isEmpty())
    <p class="uk-text-center">لا توجد فواتير حالياً.</p>
@else
<table class="uk-table uk-table-striped uk-table-hover">
    <thead>
        <tr>
            <th>#</th>
            <th>اسم الفاتورة</th>
            <th>المبلغ</th>
            <th>التاريخ</th>
            <th>تحميل</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <td>{{ $invoice->id }}</td>
                <td>{{ $invoice->title }}</td>
                <td>{{ number_format($invoice->amount, 2) }} شيكل</td>
                <td>{{ $invoice->date }}</td>
                <td>
                    @if($invoice->file_path)
                        <a href="{{ asset('storage/'.$invoice->file_path) }}" class="uk-button uk-button-primary uk-button-small" download>تحميل</a>
                    @else
                        <span class="uk-text-muted">لا يوجد ملف</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endif
