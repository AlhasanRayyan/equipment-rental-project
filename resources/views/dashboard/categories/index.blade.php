@extends('layouts.app')

@section('styles')
    <style>
        .category-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.5rem;
        }

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
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">إدارة فئات المعدات</h1>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fas fa-plus fa-sm me-2"></i>إضافة فئة جديدة
            </button>
        </div>

        @include('partials.alerts')

        <div class="card shadow">
            <div class="card-header py-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary mb-2 mb-md-0">
                        <i class="fas fa-list me-2"></i>قائمة الفئات ({{ $categories->total() }})
                    </h6>
                    <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex"
                        style="max-width: 400px; width: 100%;">
                        <input type="text" name="query" class="form-control" placeholder="ابحث بالاسم أو الوصف..."
                            value="{{ $query ?? '' }}">
                        <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                        @if ($query ?? '')
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ms-2"
                                title="إلغاء البحث"><i class="fas fa-times"></i></a>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>الفئة</th>
                                <th>الوصف</th>
                                <th class="text-center">المعدات المرتبطة</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $category->image_url ? asset('storage/' . $category->image_url) : asset('assets/img/default-category.png') }}"
                                                alt="صورة الفئة" class="category-image me-3">
                                            <div class="fw-bold">{{ Str::limit($category->category_name, 30) }}</div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($category->description, 50) ?? 'لا يوجد وصف.' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.categories.showEquipment', $category) }}"
                                            class="btn btn-sm btn-info">{{ $category->equipment_count }} معدة</a>
                                    </td>
                                    <td class="text-center">
                                        @if ($category->is_active)
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
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#editCategoryModal{{ $category->id }}"><i
                                                            class="fas fa-edit text-warning"></i> تعديل الفئة</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item text-danger" href="#"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteCategoryModal{{ $category->id }}"><i
                                                            class="fas fa-trash"></i> حذف الفئة</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center p-4">لا توجد فئات معدات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">{{ $categories->links() }}</div>
            </div>
        </div>
    </div>

    {{-- نافذة إنشاء فئة جديدة --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">إضافة فئة جديدة</h5><button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم الفئة</label>
                            <input type="text" name="category_name" class="form-control" required>
                            @error('category_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3"><label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">صورة الفئة</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            @error('image')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" id="createIsActive" class="form-check-input"
                                value="1" checked>
                            <label class="form-check-label" for="createIsActive">نشط</label>
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

    @foreach ($categories as $category)
        {{-- نافذة تعديل الفئة --}}
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST"
                        enctype="multipart/form-data"> {{-- **تأكد من enctype** --}}
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">تعديل: {{ $category->category_name }}</h5><button type="button"
                                class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">اسم الفئة</label>
                                <input type="text" name="category_name" class="form-control"
                                    value="{{ old('category_name', $category->category_name) }}" required>
                                {{-- استخدم old() للحفاظ على القيمة في حال وجود خطأ --}}
                                @error('category_name')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea> {{-- استخدم old() هنا --}}
                                @error('description')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">صورة الفئة</label>
                                @if ($category->image_url)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $category->image_url) }}" alt="صورة حالية"
                                            class="category-image" style="width: 80px; height: 80px;">
                                        <small class="text-muted d-block">الصورة الحالية (يمكنك رفع صورة جديدة
                                            لاستبدالها)</small>
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control" accept="image/*">
                                @error('image')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_active" id="editIsActive{{ $category->id }}"
                                    class="form-check-input" value="1"
                                    {{ old('is_active', $category->is_active) ? 'checked' : '' }}> {{-- استخدم old() هنا --}}
                                <label class="form-check-label" for="editIsActive{{ $category->id }}">نشط</label>
                                @error('is_active')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
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

        {{-- نافذة حذف الفئة --}}
        <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">تأكيد الحذف</h5><button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف الفئة <strong>{{ $category->category_name }}</strong>؟</p>
                            @if ($category->equipment_count > 0)
                                <div class="alert alert-danger" role="alert">
                                    هذه الفئة مرتبطة بـ <strong>{{ $category->equipment_count }}</strong> معدة. لا يمكن
                                    حذفها حتى يتم نقل هذه المعدات إلى فئة أخرى أو حذفها.
                                </div>
                            @else
                                <div class="alert alert-warning" role="alert">
                                    سيتم حذف هذه الفئة بشكل دائم.
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger"
                                {{ $category->equipment_count > 0 ? 'disabled' : '' }}>حذف</button>
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
        });
    </script>
@endpush
