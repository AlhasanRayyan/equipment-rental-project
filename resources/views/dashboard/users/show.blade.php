@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل المستخدم</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع لقائمة المستخدمين
        </a>
    </div>

    {{-- بطاقة بيانات المستخدم الأساسية --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
            <span class="text-muted"> (ID: {{ $user->id }}) </span>
        </div>
        <div class="card-body">
            <p><strong>البريد الإلكتروني:</strong> {{ $user->email }}</p>
            <p><strong>رقم الهاتف:</strong> {{ $user->phone ?? '-' }}</p>
            <p><strong>الدور:</strong>
                @if ($user->role === 'admin')
                    <span class="badge bg-danger">مدير</span>
                @else
                    <span class="badge bg-primary">مستخدم</span>
                @endif
            </p>
            <p><strong>الحالة:</strong>
                @if ($user->is_active)
                    <span class="badge bg-success">نشط</span>
                @else
                    <span class="badge bg-warning text-dark">غير نشط</span>
                @endif
            </p>
            <p class="text-muted small mb-0">
                تم إنشاء الحساب في: {{ $user->created_at?->format('Y-m-d H:i') }}
            </p>
        </div>
    </div>

    {{-- سجل الإيجارات --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>سجل الإيجارات (كمستأجر)</strong>
        </div>
        <div class="card-body">
            @if($rentals->isEmpty())
                <p class="text-muted mb-0">لا يوجد حجوزات مرتبطة بهذا المستخدم.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>رقم الحجز</th>
                                <th>تاريخ الحجز</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $index => $booking)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>#{{ $booking->id }}</td>
                                    <td>{{ $booking->created_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $booking->status ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- الشكاوى و الاستفسارات --}}
    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>الشكاوى و الاستفسارات المرسلة من هذا المستخدم</strong>
        </div>
        <div class="card-body">
            @if($complaints->isEmpty())
                <p class="text-muted mb-0">لا توجد شكاوى أو استفسارات مسجَّلة لهذا المستخدم.</p>
            @else
                <div class="list-group">
                    @foreach($complaints as $msg)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-secondary">
                                        {{ $msg->message_type === 'complaint' ? 'شكوى' : 'استفسار' }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    {{ $msg->created_at?->format('Y-m-d H:i') }}
                                </small>
                            </div>
                            <p class="mb-0 mt-2" style="white-space: pre-line;">
                                {{ $msg->content }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
