@extends('layouts.app')


@section('styles')
    <style>
        .avatar-lg-placeholder {
            width: 80px;
            height: 80px;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 2rem;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">تفاصيل الشكوى/الاستفسار</h1>
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-right fa-sm me-2"></i>العودة للشكاوى
            </a>
        </div>

        @include('partials.alerts')

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>معلومات الشكوى #{{ $message->id }}
                </h6>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                        data-bs-toggle="dropdown">إجراءات</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if (!$message->is_read)
                            <li><a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('mark-read-form-{{ $message->id }}').submit();"><i
                                        class="fas fa-check-double text-success"></i> تمييز كمقروءة</a></li>
                            <form id="mark-read-form-{{ $message->id }}"
                                action="{{ route('admin.complaints.markAsRead', $message) }}" method="POST" class="d-none">
                                @csrf</form>
                        @endif
                        @if (!$message->is_resolved)
                            {{-- تم التعديل --}}
                            <li><a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('resolve-form-{{ $message->id }}').submit();"><i
                                        class="fas fa-check-circle text-success"></i> وضع علامة تم الحل</a></li>
                            <form id="resolve-form-{{ $message->id }}"
                                action="{{ route('admin.complaints.resolve', $message) }}" method="POST" class="d-none">
                                @csrf</form>
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                data-bs-target="#deleteComplaintModal{{ $message->id }}"><i class="fas fa-trash"></i> حذف
                                الشكوى</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar-lg-placeholder me-4">
                                <span>{{ mb_substr($message->sender->first_name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $message->sender->first_name ?? 'مستخدم محذوف' }}
                                    {{ $message->sender->last_name ?? '' }}</h5>
                                <p class="text-muted mb-0">{{ $message->sender->email ?? 'N/A' }}</p>
                                <p class="text-muted small mb-0">تاريخ الإرسال:
                                    {{ $message->created_at->format('Y-m-d H:i') }}
                                    ({{ $message->created_at->diffForHumans() }})</p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6>نوع الرسالة: <span class="badge bg-info">{{ ucfirst($message->message_type) }}</span></h6>
                            <h6>الحالة:
                                @if ($message->is_resolved)
                                    {{-- تم التعديل --}}
                                    <span class="badge bg-success">تم الحل</span>
                                @elseif ($message->is_read)
                                    <span class="badge bg-primary">مقروءة</span>
                                @else
                                    <span class="badge bg-warning text-dark">جديدة</span>
                                @endif
                            </h6>
                            @if ($message->booking)
                                <h6>مرتبطة بالحجز رقم: <a href="" {{-- نفترض وجود مسار لعرض تفاصيل الحجز --}} {{-- <h6>مرتبطة بالحجز رقم: <a href="{{ route('admin.bookings.show', $message->booking_id) }}"
                                        class="text-primary fw-bold">#{{ $message->booking_id }}</a></h6> --}}
                                        {{-- نفترض وجود مسار لعرض تفاصيل الحجز --}} <p class="text-muted small">المعدة:
                                        {{ $message->booking->equipment->name ?? 'N/A' }}</p>
                            @endif
                        </div>

                        <div class="mb-4 p-3 bg-light rounded">
                            <h5 class="text-primary mb-3">محتوى الشكوى/الاستفسار:</h5>
                            <p class="lead">{{ $message->content }}</p>
                        </div>

                        {{-- يمكن إضافة قسم للرد على الشكوى هنا --}}
                        {{-- <div class="mt-5">
                            <h6>الرد على الشكوى:</h6>
                            <form action="{{ route('admin.complaints.reply', $message) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="reply_content" class="form-control" rows="5" placeholder="اكتب ردك هنا..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">إرسال الرد</button>
                            </form>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- نافذة حذف الشكوى (مكررة من index.blade.php) --}}
    <div class="modal fade" id="deleteComplaintModal{{ $message->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.complaints.destroy', $message) }}" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد حذف الشكوى</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>هل أنت متأكد من حذف هذه الشكوى/الاستفسار من
                            <strong>{{ $message->sender->first_name ?? 'مستخدم' }}</strong>؟
                        </p>
                        <div class="alert alert-danger" role="alert">
                            سيتم حذف هذه الشكوى بشكل دائم ولن يمكن استرجاعها.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
