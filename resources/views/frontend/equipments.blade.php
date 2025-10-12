@extends('layouts.master')

@section('title', 'المعدات')

@section('content')

    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">المعدات</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ url('/') }}">الصفحة الرئيسية</a></li>
                            <li><span>المعدات</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- start search part -->
    <div class="section-find section-find1">
        <div class="uk-container">
            <div class="find-box">
                <div class="find-box__title"><span>ابحث في المعدة</span></div>
                <div class="find-box__form">
                    <form action="{{ route('equipments') }}" method="GET">
                        <div class="uk-grid uk-grid-medium uk-flex-middle uk-child-width-1-3@m uk-child-width-1-2@s"
                            data-uk-grid>
                            <div>
                                <div class="uk-inline uk-width-1-1">
                                    <select class="uk-select uk-form-large" name="category">
                                        <option value="">إختر الصنف</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ request('category') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/truck.svg') }}"
                                            alt="truck" data-uk-svg></span>
                                </div>
                            </div>
                            <div>
                                <div class="uk-inline uk-width-1-1">
                                    <input class="uk-input uk-form-large uk-width-1-1" type="text" name="query"
                                        placeholder="اسم المعدة" value="{{ request('query') }}">
                                    <span class="uk-form-icon"><img src="{{ asset('assets/home/img/icons/derrick.svg') }}"
                                            alt="derrick" data-uk-svg></span>
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
    <!-- end search part -->

    <div class="page-content">
        <div class="uk-section-large uk-container">
            <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                @forelse($equipments as $equipment)
                    <div>
                        <div class="blog-item">
                            <div class="blog-item__media">
                                <a href="#!">
                                    <img src="{{ asset('assets/home/img/' . $equipment->image_url) }}"
                                        alt="{{ $equipment->name }}">
                                </a>
                                <div class="blog-item__category">{{ $equipment->category->name }}</div>
                            </div>
                            <div class="blog-item__body">
                                <div class="blog-item__info">
                                    <div class="blog-item__date">{{ $equipment->created_at->format('d M') }}</div>
                                    <div class="blog-item__author"> بواسطة <a
                                            href="#">{{ $equipment->owner->name }}</a></div>
                                </div>
                                <div class="blog-item__title">{{ $equipment->name }}</div>
                                <div class="blog-item__intro">{{ Str::limit($equipment->description, 100) }}</div>
                                <div class="rating">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input value="{{ $i }}" name="rate{{ $equipment->id }}"
                                            id="star{{ $i }}_{{ $equipment->id }}" type="radio"
                                            {{ $equipment->average_rating >= $i ? 'checked' : '' }}>
                                            {{-- ارجعيلها --}}
                                        <label title="text" for="star{{ $i }}_{{ $equipment->id }}"></label>
                                    @endfor
                                </div>
                            </div>
                            <div class="blog-item__bottom">
                                <a class="link-more" href="#"> /{{-- {{ route('equipments.show', $equipment->id ?? 0) }} --}}
                                    <span>إقرأ أكثر</span>
                                    <img class="makos" src="{{ asset('assets/home/img/icons/arrow.svg') }}" alt="arrow"
                                        data-uk-svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p>لا توجد معدات متاحة حالياً.</p>
                @endforelse
            </div>

            <!-- pagination -->
            <div class="uk-margin-large-top">
                {{-- ارجعيلها --}}
            {{ $equipments->links() }}
            </div>
        </div>
    </div>

@endsection
