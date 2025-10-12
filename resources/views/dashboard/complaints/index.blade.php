@extends('layouts.app')

@section('styles')
    <style>
        .action-dropdown .dropdown-menu {
            min-width: 200px;
            border-radius: 0.5rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
        }

        .action-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem 1rem;
        }

        .action-dropdown .dropdown-item i {
            width: 18px;
            text-align: center;
            opacity: 0.7;
        }

        .avatar-placeholder {
            width: 40px;
            height: 40px;
            background-color: #4e73df;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة الشكاوى والاستفسارات</h1>
        </div>

        @include('partials.alerts')

        <div class="card shadow">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary mb-2 mb-md-0">
                        <i class="fas fa-headset me-2"></i>قائمة الشكاوى ({{ $complaints->total() }})
                    </h6>
                    <form id="complaintFilterForm" action="{{ route('admin.complaints.index') }}" method="GET"
                        class="d-flex flex-wrap" style="max-width: 800px; width: 100%;">
                        <input type="text" name="query" class="form-control me-2 mb-2 mb-md-0 flex-grow-1"
                            placeholder="ابحث في المحتوى أو المرسل..." value="{{ $query ?? '' }}">

                        <select name="status" id="statusFilterSelect" class="form-select me-2 mb-2 mb-md-0"
                            style="width: auto;">
                            <option value="unread" {{ $statusFilter == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                            <option value="read" {{ $statusFilter == 'read' ? 'selected' : '' }}>مقروءة</option>
                            <option value="resolved" {{ $statusFilter == 'resolved' ? 'selected' : '' }}>تم الحل</option>
                            <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>جميع الحالات</option>
                        </select>

                        <select name="type" id="typeFilterSelect" class="form-select me-2 mb-2 mb-md-0"
                            style="width: auto;">
                            <option value="all" {{ $typeFilter == 'all' ? 'selected' : '' }}>جميع الأنواع</option>
                            @foreach ($messageTypes as $type)
                                <option value="{{ $type }}" {{ $typeFilter == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary ms-2 mb-2 mb-md-0"><i
                                class="fas fa-search"></i></button>
                        @if ($query || $statusFilter != 'unread' || $typeFilter != 'all')
                            <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary ms-2 mb-2 mb-md-0"
                                title="إلغاء البحث والفلاتر"><i class="fas fa-times"></i></a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>المرسل</th>
                                <th>الموضوع/المحتوى</th>
                                <th>النوع</th>
                                <th>الحالة</th>
                                <th>تاريخ الإرسال</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($complaints as $complaint)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder me-3">
                                                <span>{{ mb_substr($complaint->sender->first_name ?? 'U', 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $complaint->sender->first_name ?? 'مستخدم محذوف' }}
                                                    {{ $complaint->sender->last_name ?? '' }}</div>
                                                <div class="text-muted small">{{ $complaint->sender->email ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($complaint->content, 60) }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($complaint->message_type) }}</span>
                                    </td>
                                    <td>
                                        {{-- تم التعديل لعرض الحالة بشكل أدق --}}
                                        @if ($complaint->is_resolved)
                                            <span class="badge bg-success">تم الحل</span>
                                        @elseif ($complaint->is_read)
                                            <span class="badge bg-primary">مقروءة</span>
                                        @else
                                            <span class="badge bg-warning text-dark">جديدة</span>
                                        @endif
                                    </td>
                                    <td>{{ $complaint->created_at->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">إجراءات</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item"
                                                        href="{{ route('admin.complaints.show', $complaint) }}"><i
                                                            class="fas fa-eye text-primary"></i> عرض التفاصيل</a></li>
                                                @if (!$complaint->is_read)
                                                    <li><a class="dropdown-item" href="#"
                                                            onclick="event.preventDefault(); document.getElementById('mark-read-form-{{ $complaint->id }}').submit();"><i
                                                                class="fas fa-check-double text-success"></i> تمييز
                                                            كمقروءة</a></li>
                                                    <form id="mark-read-form-{{ $complaint->id }}"
                                                        action="{{ route('admin.complaints.markAsRead', $complaint) }}"
                                                        method="POST" class="d-none">@csrf</form>
                                                @endif
                                                @if (!$complaint->is_resolved)
                                                    {{-- تم التعديل لعدم عرض الزر إذا كانت محلولة --}}
                                                    <li><a class="dropdown-item" href="#"
                                                            onclick="event.preventDefault(); document.getElementById('resolve-form-{{ $complaint->id }}').submit();"><i
                                                                class="fas fa-check-circle text-success"></i> وضع علامة تم
                                                            الحل</a></li>
                                                    <form id="resolve-form-{{ $complaint->id }}"
                                                        action="{{ route('admin.complaints.resolve', $complaint) }}"
                                                        method="POST" class="d-none">@csrf</form>
                                                @endif
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#deleteComplaintModal{{ $complaint->id }}"><i
                                                            class="fas fa-trash text-danger"></i> حذف الشكوى</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">لا توجد شكاوى أو استفسارات حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $complaints->links() }}</div>
            </div>
        </div>
    </div>

    @foreach ($complaints as $complaint)
        {{-- نافذة حذف الشكوى --}}
        <div class="modal fade" id="deleteComplaintModal{{ $complaint->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.complaints.destroy', $complaint) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">تأكيد حذف الشكوى</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف هذه الشكوى/الاستفسار من
                                <strong>{{ $complaint->sender->first_name ?? 'مستخدم' }}</strong>؟
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
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // **الـ JavaScript لجعل الفلاتر تعمل تلقائياً**
            const complaintFilterForm = document.getElementById('complaintFilterForm');
            const statusFilterSelect = document.getElementById('statusFilterSelect');
            const typeFilterSelect = document.getElementById('typeFilterSelect');

            if (statusFilterSelect) {
                statusFilterSelect.addEventListener('change', function() {
                    complaintFilterForm.submit();
                });
            }
            if (typeFilterSelect) {
                typeFilterSelect.addEventListener('change', function() {
                    complaintFilterForm.submit();
                });
            }
        });
    </script>
@endpush
