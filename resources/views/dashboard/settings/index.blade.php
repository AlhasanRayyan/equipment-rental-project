@extends('layouts.app')

@section('styles')
    <style>
        .setting-description {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .action-dropdown .dropdown-menu {
            min-width: 150px;
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
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة إعدادات النظام</h1>
            {{-- يمكن إضافة زر لإنشاء إعداد جديد إذا لزم الأمر (إذا تم تفعيل دالة store في Controller) --}}
            {{-- <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createSettingModal">
                <i class="fas fa-plus fa-sm me-2"></i> إضافة إعداد جديد
            </button> --}}
            <div>
                <a href="{{ route('admin.settings.trash') }}" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt me-1"></i> سلة المحذوفات
                </a>
                <form action="{{ route('admin.settings.backup') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-database fa-sm me-1"></i>
                        إنشاء نسخة احتياطية
                    </button>
                </form>
            </div>
        </div>


        @include('partials.alerts')

        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-cogs me-2"></i>إعدادات النظام ({{ $settings->count() }})
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>المفتاح</th>
                                <th>القيمة</th>
                                <th>الوصف</th>
                                <th>آخر تحديث بواسطة</th>
                                <th>تاريخ التحديث</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($settings as $setting)
                                <tr>
                                    <td><span class="fw-bold">{{ $setting->setting_key }}</span></td>
                                    <td>{{ Str::limit($setting->setting_value, 50) }}</td>
                                    <td>
                                        <p class="setting-description mb-0">{{ Str::limit($setting->description, 70) }}</p>
                                    </td>
                                    <td>{{ $setting->updatedBy->first_name ?? 'N/A' }}</td> {{-- عرض اسم المستخدم الذي قام بالتحديث --}}
                                    <td>{{ $setting->updated_at->format('Y-m-d H:i') }}</td>
                                    {{-- <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">إجراءات</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editSettingModal{{ $setting->id }}"><i
                                                            class="fas fa-edit text-warning"></i> تعديل الإعداد</a></li>
                                                {{-- يمكن إضافة خيار الحذف هنا إذا تم تفعيل دالة destroy في Controller --}
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteSettingModal{{ $setting->id }}"><i
                                                            class="fas fa-trash"></i> حذف الإعداد</a></li>
                                            </ul>
                                        </div>
                                    </td> --}}
                                    {{-- <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">إجراءات</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.settings.show', $setting) }}">
                                                        <i class="fas fa-eye text-info"></i> عرض التفاصيل
                                                    </a>
                                                </li>

                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editSettingModal{{ $setting->id }}">
                                                        <i class="fas fa-edit text-warning"></i> تعديل الإعداد
                                                    </a>
                                                </li>



                                                @if (!in_array($setting->setting_key, $protectedKeys))
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteSettingModal{{ $setting->id }}">
                                                            <i class="fas fa-trash"></i> حذف (إلى السلة)
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td> --}}
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">إجراءات</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.settings.show', $setting) }}">
                                                        <i class="fas fa-eye text-info"></i> عرض التفاصيل
                                                    </a>
                                                </li>

                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editSettingModal{{ $setting->id }}">
                                                        <i class="fas fa-edit text-warning"></i> تعديل الإعداد
                                                    </a>
                                                </li>

                                                @if (!in_array($setting->setting_key, $protectedKeys))
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteSettingModal{{ $setting->id }}">
                                                            <i class="fas fa-trash"></i> حذف (إلى السلة)
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-4">لا توجد إعدادات نظام لعرضها.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($settings as $setting)
        {{-- نافذة تعديل الإعداد --}}
        <div class="modal fade" id="editSettingModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.settings.update', $setting) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل: {{ $setting->setting_key }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            {{-- <div class="mb-3">
                                <label for="setting_value_{{ $setting->id }}" class="form-label">القيمة</label>
                                @if ($setting->setting_key == 'maintenance_mode') {{-- مثال على حقل خاص للتبديل --}
                                    <select name="setting_value" id="setting_value_{{ $setting->id }}" class="form-select" required>
                                        <option value="true" {{ $setting->setting_value == 'true' ? 'selected' : '' }}>مفعل</option>
                                        <option value="false" {{ $setting->setting_value == 'false' ? 'selected' : '' }}>غير مفعل</option>
                                    </select>
                                @elseif (
                                    $setting->setting_key == 'tax_rate_percent'
                                ) {{-- مثال على حقل رقمي --}
                                    <input type="number" step="0.01" name="setting_value" id="setting_value_{{ $setting->id }}" class="form-control" value="{{ $setting->setting_value }}" required>
                                @else {{-- حقل نصي عام --}
                                    <input type="text" name="setting_value" id="setting_value_{{ $setting->id }}" class="form-control" value="{{ $setting->setting_value }}" required>
                                @endif
                                <div class="form-text setting-description">{{ $setting->description }}</div>
                            </div> --}}
                            <div class="mb-3">
                                <label for="setting_value_{{ $setting->id }}" class="form-label">القيمة</label>

                                @php
                                    $iconKeys = [
                                        'homepage_step1_icon',
                                        'homepage_step2_icon',
                                        'homepage_step3_icon',
                                        'homepage_step4_icon',
                                        'homepage_why_box1_icon',
                                        'homepage_why_box2_icon',
                                        'homepage_why_box3_icon',
                                        'homepage_why_box4_icon',
                                    ];
                                @endphp

                                @if (in_array($setting->setting_key, $iconKeys))
                                    {{-- 👇 إعداد من نوع "صورة" – نعرض صورة حالية + حقل رفع --}}
                                    @if ($setting->setting_value)
                                        <div class="mb-2">
                                            <span class="setting-description d-block mb-1">الصورة الحالية:</span>
                                            <img src="{{ asset($setting->setting_value) }}" alt="icon"
                                                style="max-width: 80px; max-height: 80px;">
                                        </div>
                                    @endif

                                    <input type="file" name="setting_file" id="setting_file_{{ $setting->id }}"
                                        class="form-control" accept="image/*">
                                @elseif ($setting->setting_key == 'maintenance_mode')
                                    <select name="setting_value" id="setting_value_{{ $setting->id }}" class="form-select"
                                        required>
                                        <option value="true" {{ $setting->setting_value == 'true' ? 'selected' : '' }}>
                                            مفعل</option>
                                        <option value="false" {{ $setting->setting_value == 'false' ? 'selected' : '' }}>
                                            غير مفعل</option>
                                    </select>
                                @elseif ($setting->setting_key == 'tax_rate_percent')
                                    <input type="number" step="0.01" name="setting_value"
                                        id="setting_value_{{ $setting->id }}" class="form-control"
                                        value="{{ $setting->setting_value }}" required>
                                @else
                                    <input type="text" name="setting_value" id="setting_value_{{ $setting->id }}"
                                        class="form-control" value="{{ $setting->setting_value }}" required>
                                @endif

                                <div class="form-text setting-description">{{ $setting->description }}</div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- نافذة حذف الإعداد (مُعلق حالياً في Controller) --}}
        {{-- <div class="modal fade" id="deleteSettingModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">تأكيد الحذف</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف الإعداد <strong>{{ $setting->setting_key }}</strong>؟</p>
                            <div class="alert alert-danger" role="alert">
                                سيتم حذف هذا الإعداد بشكل دائم وقد يؤثر على عمل النظام.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger">حذف</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
        {{-- نافذة حذف الإعداد --}}
        {{-- <div class="modal fade" id="deleteSettingModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">تأكيد الحذف</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف الإعداد <strong>{{ $setting->setting_key }}</strong>؟</p>
                            <div class="alert alert-warning" role="alert">
                                سيتم نقل هذا الإعداد إلى سلة المحذوفات، يمكنك استرجاعه لاحقًا.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger">حذف (إلى السلة)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> --}}
        @if (!in_array($setting->setting_key, $protectedKeys))
            <div class="modal fade" id="deleteSettingModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST">
                            @csrf @method('DELETE')
                            <div class="modal-header">
                                <h5 class="modal-title">تأكيد الحذف</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>هل أنت متأكد من حذف الإعداد <strong>{{ $setting->setting_key }}</strong>؟</p>
                                <div class="alert alert-warning" role="alert">
                                    سيتم نقل هذا الإعداد إلى سلة المحذوفات، يمكنك استرجاعه لاحقًا.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger">حذف (إلى السلة)</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
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
        });
    </script>
@endpush
