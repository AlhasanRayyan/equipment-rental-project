@if($favorites->isEmpty())
    <div class="uk-text-center uk-padding">
        <span data-uk-icon="icon: heart; ratio: 3" class="uk-text-muted"></span>
        <p class="uk-text-muted uk-margin-small-top">لا توجد معدات في المفضلة بعد.</p>
    </div>
@else
<div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
    @foreach($favorites as $fav)
        @if($fav->equipment)
        <div>
            <div class="blog-item">
                <div class="blog-item__media">
                    <a href="{{ route('equipments.show', $fav->equipment->id) }}">
                        @if($fav->equipment->images && $fav->equipment->images->count() > 0)
                            <img src="{{ asset('storage/' . $fav->equipment->images->first()->image_url) }}"
                                 alt="{{ $fav->equipment->name }}" data-uk-img>
                        @else
                            <img src="{{ asset('assets/home/img/equipment-default.jpg') }}"
                                 alt="صورة افتراضية" data-uk-img>
                        @endif
                    </a>
                    <div class="blog-item__category">
                        {{ $fav->equipment->category->category_name ?? 'غير مصنف' }}
                    </div>

                    {{-- ❤️ زر إزالة من المفضلة --}}
                    <form action="{{ route('favorites.destroy', $fav->id) }}"
                          method="POST"
                          style="position:absolute; top:10px; left:10px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="background:none; border:none; cursor:pointer;"
                                title="إزالة من المفضلة">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                 viewBox="0 0 24 24" fill="#e63946" stroke="#e63946"
                                 stroke-width="1.5">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                         a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                         1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="blog-item__body">
                    <div class="blog-item__info">
                        <div class="blog-item__date">{{ $fav->created_at->format('d M, Y') }}</div>
                        <div class="blog-item__author">
                            بواسطة <a href="#">{{ $fav->equipment->owner->name ?? 'غير معروف' }}</a>
                        </div>
                    </div>
                    <div class="blog-item__title">{{ $fav->equipment->name }}</div>
                    <div class="blog-item__intro">
                        {{ Str::limit($fav->equipment->description, 100) }}
                    </div>
                    <div class="uk-text-bold uk-text-primary uk-margin-small-top">
                        {{ number_format($fav->equipment->daily_rate, 2) }} $ / يوم
                    </div>
                </div>

                <div class="blog-item__bottom">
                    <a class="link-more" href="{{ route('equipments.show', $fav->equipment->id) }}">
                        <span>إقرأ أكثر</span>
                        <img class="ukos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                             alt="arrow" data-uk-svg>
                    </a>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>
@endif