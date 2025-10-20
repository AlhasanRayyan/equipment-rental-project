@extends('layouts.master')

@section('title', 'معدات المستخدم')

@section('content')
    <main class="page-main">
        <div class="page-head">
            <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
                <div class="page-head__content" data-uk-parallax="y: 0, 100">
                    <div class="uk-container">
                        <div class="header-icons"><span></span><span></span><span></span></div>
                        <div class="page-head__title">معدات المالك</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- قسم البحث --}}
        <div class="section-find section-find1">
            <div class="uk-container">
                <div class="find-box">
                    <div class="find-box__title"><span>ابحث في معداتك</span></div>
                    <div class="find-box__form">
                        <form action="{{ route('owner.equipments.search') }}" method="GET">
                            <div class="uk-grid uk-grid-medium uk-flex-middle uk-child-width-1-3@m uk-child-width-1-2@s"
                                data-uk-grid>
                                <div>
                                    <div class="uk-inline uk-width-1-1">
                                        <select class="uk-select uk-form-large" name="category">
                                            <option value="">اختر الفئة</option>
                                            @foreach ($categories->whereNull('parent_id') as $parent)
                                                <option value="{{ $parent->id }}">{{ $parent->category_name }}</option>
                                                @foreach ($parent->children as $child)
                                                    <option value="{{ $child->id }}">— {{ $child->category_name }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </select>

                                        <span class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/truck.svg') }}"
                                                alt="truck" data-uk-svg></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-large" name="q" type="text"
                                            placeholder="اسم المعدة">
                                        <span class="uk-form-icon"><img
                                                src="{{ asset('assets/home/img/icons/derrick.svg') }}" alt="derrick"
                                                data-uk-svg></span>
                                    </div>
                                </div>
                                <div>
                                    <input class="main-input uk-input uk-form-large uk-width-1-1" type="submit"
                                        value="ابحث هنا">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- عرض المعدات --}}
        <div class="page-content">
            <div class="uk-section-large uk-container">
                <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                    @forelse ($equipments as $equipment)
                        <div>
                            <div class="blog-item">
                                <div class="blog-item__media">
                                    <a href="{{ route('equipments.show', $equipment->id) }}">
                                        @if ($equipment->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $equipment->images->first()->image_url) }}"
                                                alt="{{ $equipment->name }}" class="equipment-img">
                                        @else
                                            <img src="{{ asset('images/default-equipment.png') }}" alt="No Image"
                                                class="equipment-img">
                                        @endif
                                    </a>
                                    <div class="blog-item__category">{{ $equipment->status }}</div>
                                </div>
                                <div class="blog-item__body">
                                    <div class="blog-item__info">
                                        <div class="blog-item__date">{{ $equipment->created_at->format('d M') }}</div>
                                    </div>
                                    <div class="blog-item__title">{{ $equipment->name }}</div>
                                    <div class="blog-item__intro">{{ Str::limit($equipment->description, 60) }}</div>
                                    <div class="rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <input value="{{ $i }}" name="rate{{ $equipment->id }}"
                                                id="star{{ $i }}-{{ $equipment->id }}" type="radio"
                                                {{ $equipment->rating == $i ? 'checked' : '' }}>
                                            <label for="star{{ $i }}-{{ $equipment->id }}"></label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="blog-item__bottom">
                                    <a class="link-more" href="{{ route('owner.equipments.edit', $equipment->id) }}">
                                        <span>تعديل</span>
                                        <img class="makos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                                            alt="arrow" data-uk-svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="uk-text-center uk-width-1-1">لا توجد معدات مضافة بعد.</p>
                    @endforelse
                </div>

                {{-- الصفحات --}}
                <div class="uk-flex uk-flex-center uk-margin-large-top">
                    {{ $equipments->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </main>
@endsection
