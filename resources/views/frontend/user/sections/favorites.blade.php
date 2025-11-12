@if($favorites->isEmpty())
    <p class="uk-text-center">لا توجد معدات في المفضلة.</p>
@else
<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-3@m" data-uk-grid>
    @foreach($favorites as $fav)
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-text-center">
                <h5 class="uk-card-title">{{ $fav->equipment->name ?? 'تم حذف المعدة' }}</h5>
                <p>الفئة: {{ $fav->equipment->category ?? '-' }}</p>
                <button class="uk-button uk-button-danger uk-button-small">إزالة</button>
            </div>
        </div>
    @endforeach
</div>
@endif
