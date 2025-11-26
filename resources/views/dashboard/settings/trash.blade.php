@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">سلة المحذوفات - إعدادات النظام</h1>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع للإعدادات
        </a>
    </div>

    @include('partials.alerts')

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <form action="{{ route('admin.settings.trash') }}" method="GET" class="d-flex" style="max-width: 400px; width: 100%;">
                <input type="text" name="query" class="form-control" placeholder="ابحث بالمفتاح أو الوصف..."
                       value="{{ $query ?? '' }}">
                <button class="btn btn-primary ms-2">بحث</button>
            </form>

            @if ($settings->count() > 0)
                <div>
                    <form action="{{ route('admin.settings.restoreAll') }}" method="POST" class="d-inline-block">
                        @csrf
                        <button class="btn btn-success btn-sm"
                                onclick="return confirm('استرجاع جميع الإعدادات من السلة؟')">
                            استرجاع الكل
                        </button>
                    </form>
                    <form action="{{ route('admin.settings.forceDeleteAll') }}" method="POST" class="d-inline-block ms-2">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('حذف نهائي لجميع الإعدادات (غير المحمية)؟ لا يمكن التراجع!')">
                            حذف نهائي للكل
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>المفتاح</th>
                        <th>القيمة</th>
                        <th>الوصف</th>
                        <th>تاريخ الحذف</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                        <tr>
                            <td class="fw-bold">{{ $setting->setting_key }}</td>
                            <td>{{ Str::limit($setting->setting_value, 40) }}</td>
                            <td>{{ Str::limit($setting->description, 50) }}</td>
                            <td>{{ $setting->deleted_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.settings.restore', $setting->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    <button class="btn btn-sm btn-success">استرجاع</button>
                                </form>

                                @php
                                    $protectedKeys = ['platform_name','contact_email','support_phone','homepage_title'];
                                @endphp

                                @if (!in_array($setting->setting_key, $protectedKeys))
                                    <form action="{{ route('admin.settings.forceDelete', $setting->id) }}" method="POST" class="d-inline-block ms-1">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('حذف نهائي لهذا الإعداد؟ لا يمكن التراجع!')">
                                            حذف نهائي
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center p-4">سلة المحذوفات فارغة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $settings->links() }}
        </div>
    </div>
</div>
@endsection
