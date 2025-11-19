@extends('layouts.app')

@section('styles')
<style>
    .avatar-placeholder {
        width: 40px; height: 40px; border-radius: 50%;
        background:#4e73df; color:#fff; display:flex;
        align-items:center; justify-content:center;
        font-weight:bold; font-size:1.1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">سلة المحذوفات - الشكاوى والاستفسارات</h1>
        <div>
            <a href="{{ route('admin.complaints.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> رجوع للشكاوى
            </a>

            @if($complaints->count() > 0)
                <form action="{{ route('admin.complaints.restoreAll') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-undo"></i> استعادة الكل
                    </button>
                </form>

                <form action="{{ route('admin.complaints.forceDeleteAll') }}" method="POST" class="d-inline"
                      onsubmit="return confirm('حذف نهائي لكل الشكاوى؟');">
                    @csrf @method('DELETE')
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
                الشكاوى المحذوفة ({{ $complaints->total() }})
            </h6>
            <form action="{{ route('admin.complaints.trash') }}" method="GET"
                  class="d-flex" style="max-width:450px;width:100%;">
                <input type="text" name="query" class="form-control" placeholder="ابحث في المحتوى أو المرسل..."
                       value="{{ $query ?? '' }}">
                <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                @if($query ?? '')
                    <a href="{{ route('admin.complaints.trash') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-placeholder me-3">
                                            <span>{{ mb_substr($complaint->sender->first_name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold">
                                                {{ $complaint->sender->first_name ?? 'مستخدم' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ Str::limit($complaint->content, 40) }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        {{ $complaint->message_type === 'complaint' ? 'شكوى' : 'استفسار' }}
                                    </span>
                                </td>
                                <td class="text-end text-muted small">
                                    {{ $complaint->deleted_at?->diffForHumans() }}
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.complaints.restore', $complaint->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success"><i class="fas fa-undo"></i> استعادة</button>
                                    </form>
                                    <form action="{{ route('admin.complaints.forceDelete', $complaint->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('حذف نهائي؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i> حذف نهائي</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center p-4">لا توجد شكاوى في سلة المحذوفات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
