@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل الإعداد</h1>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> رجوع لقائمة الإعدادات
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <strong>{{ $adminSetting->setting_key }}</strong>
        </div>
        <div class="card-body">
            <p><strong>القيمة:</strong></p>
            <p class="mb-3" style="white-space: pre-wrap;">
                {{ $adminSetting->setting_value }}
            </p>

            <p><strong>الوصف:</strong></p>
            <p class="mb-3 text-muted">
                {{ $adminSetting->description ?? 'لا يوجد وصف' }}
            </p>

            <p><strong>آخر تحديث بواسطة:</strong>
                {{ $adminSetting->updatedBy->first_name ?? 'N/A' }}
            </p>

            <p class="text-muted small mb-0">
                آخر تحديث في: {{ $adminSetting->updated_at?->format('Y-m-d H:i') }}
            </p>
        </div>
    </div>

</div>
@endsection
