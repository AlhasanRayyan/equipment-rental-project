@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">سلة المحذوفات - المعدات</h1>
            <div>
                <a href="{{ route('admin.equipment.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع لقائمة المعدات
                </a>

                @if ($equipment->count() > 0)
                    <form action="{{ route('admin.equipment.restoreAll') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-undo"></i> استعادة الكل
                        </button>
                    </form>

                    <form action="{{ route('admin.equipment.forceDeleteAll') }}" method="POST" class="d-inline"
                        onsubmit="return confirm('هل أنت متأكد من حذف جميع المعدات نهائياً؟');">
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
                    <i class="fas fa-hard-hat me-2"></i>المعدات المحذوفة ({{ $equipment->total() }})
                </h6>
                <form action="{{ route('admin.equipment.trash') }}" method="GET" class="d-flex"
                    style="max-width: 400px; width:100%;">
                    <input type="text" name="query" class="form-control" placeholder="ابحث بالاسم أو الوصف..."
                        value="{{ $query ?? '' }}">
                    <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                    @if ($query ?? '')
                        <a href="{{ route('admin.equipment.trash') }}" class="btn btn-secondary ms-2">
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
                                <th>المعدة</th>
                                <th>المالك</th>
                                <th>الفئة</th>
                                <th>تاريخ الحذف</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipment as $item)
                                <tr>
                                    <td>{{ Str::limit($item->name, 30) }}</td>
                                    <td>{{ $item->owner->first_name ?? 'N/A' }}</td>
                                    <td>{{ $item->category->category_name ?? '-' }}</td>
                                    <td>{{ $item->deleted_at?->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.equipment.restore', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i> استعادة
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.equipment.forceDelete', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('حذف نهائي؟ لا يمكن التراجع.');">
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
                                    <td colspan="5" class="text-center p-4">لا توجد معدات في سلة المحذوفات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $equipment->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
