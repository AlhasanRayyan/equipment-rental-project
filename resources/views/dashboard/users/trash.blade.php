@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">سلة المحذوفات - المستخدمين</h1>
            <div>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right"></i> رجوع لقائمة المستخدمين
                </a>

                @if ($users->count() > 0)
                    <form action="{{ route('admin.users.restoreAll') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-undo"></i> استعادة الكل
                        </button>
                    </form>

                    <form action="{{ route('admin.users.forceDeleteAll') }}" method="POST" class="d-inline"
                        onsubmit="return confirm('هل أنت متأكد من حذف كل المستخدمين نهائياً؟');">
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
                    <i class="fas fa-users-slash me-2"></i>المستخدمون المحذوفون ({{ $users->total() }})
                </h6>
                <form action="{{ route('admin.users.trash') }}" method="GET" class="d-flex"
                    style="max-width: 400px; width:100%">
                    <input type="text" name="query" class="form-control" placeholder="ابحث بالاسم أو البريد..."
                        value="{{ $query ?? '' }}">
                    <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                    @if ($query ?? '')
                        <a href="{{ route('admin.users.trash') }}" class="btn btn-secondary ms-2">
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
                                <th>المستخدم</th>
                                <th>البريد</th>
                                <th>تاريخ الحذف</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->deleted_at?->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-undo"></i> استعادة
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST"
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
                                    <td colspan="4" class="text-center p-4">لا يوجد مستخدمون في سلة المحذوفات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
