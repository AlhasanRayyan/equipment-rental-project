@extends('layouts.app')

@section('styles')
<style>
    .category-image {
        width: 40px; height: 40px; object-fit: cover; border-radius: .5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">سلة المحذوفات - فئات المعدات</h1>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> رجوع للفئات
            </a>

            @if($categories->count() > 0)
                <form action="{{ route('admin.categories.restoreAll') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-undo"></i> استعادة الكل
                    </button>
                </form>

                <form action="{{ route('admin.categories.forceDeleteAll') }}" method="POST" class="d-inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف جميع الفئات نهائياً؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash-alt"></i> حذف الكل نهائياً
                    </button>
                </form>
            @endif
        </div>
    </div>

    @include('partials.alerts')

    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-list me-2"></i>الفئات المحذوفة ({{ $categories->total() }})
            </h6>
            <form action="{{ route('admin.categories.trash') }}" method="GET"
                  class="d-flex" style="max-width: 400px; width:100%;">
                <input type="text" name="query" class="form-control" placeholder="ابحث بالاسم أو الوصف..."
                       value="{{ $query ?? '' }}">
                <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                @if($query ?? '')
                    <a href="{{ route('admin.categories.trash') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>الفئة</th>
                            <th>الوصف</th>
                            <th>تاريخ الحذف</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ Str::limit($category->description, 60) }}</td>
                                <td>{{ $category->deleted_at?->diffForHumans() }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.categories.restore', $category->id) }}"
                                          method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-undo"></i> استعادة
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.categories.forceDelete', $category->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('حذف نهائي؟ لا يمكن التراجع.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash-alt"></i> حذف نهائي
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-4">لا توجد فئات في سلة المحذوفات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
