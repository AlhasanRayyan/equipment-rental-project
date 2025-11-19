@extends('layouts.app')

@section('styles')
    {{-- تم حذف مكتبات Select2 CSS لأنها لم تعد مطلوبة هنا --}}
    <style>
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            background-color: var(--bs-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            border-radius: 50%;
        }

        .action-dropdown .dropdown-menu {
            min-width: 200px;
            /* تم تعديل العرض قليلاً */
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

        /* تم حذف Select2-related styles */
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة المستخدمين</h1>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fas fa-user-plus fa-sm me-2"></i>إضافة مستخدم جديد
            </button>
        </div> --}}<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">إدارة المستخدمين</h1>
    <div>
        <a href="{{ route('admin.users.trash') }}" class="btn btn-outline-danger me-2">
            <i class="fas fa-trash-alt"></i> سلة المحذوفات
        </a>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus fa-sm me-2"></i>إضافة مستخدم جديد
        </button>
    </div>
</div>


        @include('partials.alerts') {{-- تأكد من وجود ملف alerts.blade.php في مسار partials --}}

        <div class="card shadow">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary mb-2 mb-md-0">
                        <i class="fas fa-users me-2"></i>قائمة المستخدمين ({{ $users->total() }})
                    </h6>
                    <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex"
                        style="max-width: 400px; width: 100%;">
                        <input type="text" name="query" class="form-control" placeholder="ابحث بالاسم أو البريد..."
                            value="{{ $query ?? '' }}">
                        <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                        @if ($query ?? '')
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2" title="إلغاء البحث"><i
                                    class="fas fa-times"></i></a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>المستخدم</th>
                                <th>الدور</th> {{-- عمود الدور --}}
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder me-3">
                                                <span>{{ mb_substr($user->first_name, 0, 1) }}</span> {{-- استخدام first_name --}}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                {{-- استخدام first_name و last_name --}}
                                                <div class="text-muted small">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{-- عرض الدور (user/admin) --}}
                                        @if ($user->role === 'admin')
                                            <span class="badge bg-danger">مدير</span>
                                        @else
                                            <span class="badge bg-primary">مستخدم</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($user->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-warning text-dark">غير نشط</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">إجراءات</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.users.show', $user) }}">
                                                        <i class="fas fa-eye text-info"></i> عرض التفاصيل
                                                    </a>
                                                </li>
                                                {{-- لا تسمح بتعديل أو حذف المستخدم الأول (Super Admin) أو المستخدم الحالي --}}
                                                @if ($user->id !== 1 && auth()->id() !== $user->id)
                                                    @if ($user->is_active)
                                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                                data-bs-target="#deactivateUserModal{{ $user->id }}"><i
                                                                    class="fas fa-times-circle text-warning"></i> تعطيل
                                                                المستخدم</a></li>
                                                    @else
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="event.preventDefault(); document.getElementById('activate-form-{{ $user->id }}').submit();"><i
                                                                    class="fas fa-check-circle text-success"></i> تفعيل
                                                                المستخدم</a>
                                                            <form id="activate-form-{{ $user->id }}"
                                                                action="{{ route('admin.users.activate', $user) }}"
                                                                method="POST" class="d-none">@csrf</form>
                                                        </li>
                                                    @endif
                                                @endif
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal{{ $user->id }}"><i
                                                            class="fas fa-edit text-info"></i> تعديل المستخدم</a></li>
                                                @if (auth()->id() != $user->id && $user->id != 1)
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item text-danger" href="#"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteUserModal{{ $user->id }}"><i
                                                                class="fas fa-trash"></i> حذف المستخدم</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center p-4">لا يوجد مستخدمون.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $users->links() }}</div>
            </div>
        </div>
    </div>

    {{-- نافذة إنشاء مستخدم جديد --}}
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة مستخدم</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">الاسم الأول</label><input type="text"
                                name="first_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">الاسم الأخير</label><input type="text"
                                name="last_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input type="email"
                                name="email" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">كلمة المرور</label><input type="password"
                                name="password" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">الدور</label>
                            <select name="role" class="form-select" required>
                                <option value="user" selected>مستخدم عادي</option>
                                <option value="admin">مدير</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">إغلاق</button><button type="submit"
                            class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($users as $user)
        <!-- نافذة تعديل المستخدم -->
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل: {{ $user->first_name }} {{ $user->last_name }}</h5><button
                                type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3"><label class="form-label">الاسم الأول</label><input type="text"
                                    name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                            </div>
                            <div class="mb-3"><label class="form-label">الاسم الأخير</label><input type="text"
                                    name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                            </div>
                            <div class="mb-3"><label class="form-label">البريد الإلكتروني</label><input type="email"
                                    name="email" class="form-control" value="{{ $user->email }}" required></div>
                            <div class="mb-3"><label class="form-label">كلمة المرور (اتركه فارغاً)</label><input
                                    type="password" name="password" class="form-control"></div>
                            <div class="mb-3">
                                <label class="form-label">الدور</label>
                                <select name="role" class="form-select">
                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>مستخدم عادي
                                    </option>
                                    {{-- لا تسمح بتغيير دور المستخدم الأول إلى غير Admin --}}
                                    @if ($user->id === 1)
                                        <option value="admin" selected>مدير</option>
                                    @else
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>مدير
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">إغلاق</button><button type="submit"
                                class="btn btn-primary">حفظ</button></div>
                    </form>
                </div>
            </div>
        </div>

        {{-- نافذة تعطيل المستخدم --}}
        <div class="modal fade" id="deactivateUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.users.deactivate', $user) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">تعطيل: {{ $user->first_name }} {{ $user->last_name }}</h5><button
                                type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من تعطيل المستخدم <strong>{{ $user->first_name }}
                                    {{ $user->last_name }}</strong>؟</p>
                            <div class="alert alert-warning" role="alert">
                                لن يتمكن المستخدم من تسجيل الدخول أو استخدام خدمات المنصة بعد تعطيل حسابه.
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">إغلاق</button><button type="submit"
                                class="btn btn-warning">تعطيل</button></div>
                    </form>
                </div>
            </div>
        </div>

        {{-- نافذة حذف المستخدم --}}
        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">تأكيد الحذف</h5><button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف المستخدم <strong>{{ $user->first_name }}
                                    {{ $user->last_name }}</strong>؟</p>
                            <div class="alert alert-danger" role="alert">
                                سيتم حذف جميع البيانات المرتبطة بهذا المستخدم بشكل دائم (المعدات، الحجوزات، إلخ).
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">إلغاء</button><button type="submit"
                                class="btn btn-danger">حذف</button></div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    {{-- تم حذف مكتبة Select2 JS لأنها لم تعد مطلوبة هنا --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة Tooltips (لا تزال مفيدة لبعض الأزرار)
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // يمكن إزالة أي كود Select2 هنا
        });
    </script>
@endpush
