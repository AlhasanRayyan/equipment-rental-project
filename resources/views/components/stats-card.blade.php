{{-- resources/views/components/stats-card.blade.php --}}
@props([
    // أيقونة FontAwesome، مثال: "fas fa-users"
    'icon',

    // عنوان البطاقة
    'title',

    // القيمة المراد عرضها
    'value',

    // لون البطاقة: primary, success, info, warning, danger (افتراضي 'primary')
    'color' => 'primary',

    // بادئة قبل الرقم (مثلاً $)
    'prefix' => null,

    // سطر صغير تحت الرقم
    'subtitle' => null,

    // بادج صغيرة جنب العنوان
    'badge' => null,
])

<div class="card border-left-{{ $color }} shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                {{-- العنوان + البادج جنب بعض --}}
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase">
                        {{ $title }}
                    </div>

                    @if ($badge)
                        <span class="badge bg-danger small">
                            {{ $badge }}
                        </span>
                    @endif
                </div>

                {{-- الرقم الرئيسي --}}
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    @if($prefix){{ $prefix }}@endif {{ $value }}
                </div>

                {{-- سطر توضيحي تحت الرقم (اختياري) --}}
                @if ($subtitle)
                    <div class="small text-muted mt-1">
                        {{ $subtitle }}
                    </div>
                @endif
            </div>

            {{-- الأيقونة على اليمين --}}
            <div class="col-auto">
                <i class="{{ $icon }} fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>
</div>
