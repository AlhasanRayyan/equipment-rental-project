@extends('layouts.master')

@section('title', __('home.title'))

@section('content')
    <div class="section-slideshow">
        <div class="uk-position-relative uk-light" tabindex="-1"
            data-uk-slideshow="animation: fade; min-height: 400; max-height: 700;">
            <ul class="uk-slideshow-items">
                <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-1.jpg') }}"
                        alt="{{ __('home.slideshow1_alt') }}" data-uk-cover>
                    <div class="slideshow-item__content">
                        <div class="uk-position-center uk-position-small uk-text-center">
                            <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                                <span></span><span></span><span></span>
                            </div>
                            <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">
                                {{ __('home.slideshow1_desc') }}</p>
                            <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">
                                {{ __('home.slideshow1_title') }}</h2>
                        </div>
                    </div>
                </li>
                <li class="slideshow-item"><img src="{{ asset('assets/home/img/slideshow-2.jpg') }}"
                        alt="{{ __('home.slideshow2_alt') }}" data-uk-cover>
                    <div class="slideshow-item__content">
                        <div class="uk-position-center uk-position-small uk-text-center">
                            <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                                <span></span><span></span><span></span>
                            </div>
                            <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">
                                {{ __('home.slideshow2_desc') }}</p>
                            <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">
                                {{ __('home.slideshow2_title') }}</h2>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="uk-visible@m"><a class="uk-position-center-left uk-position-small" href="#"
                    data-uk-slidenav-previous data-uk-slideshow-item="previous"></a><a
                    class="uk-position-center-right uk-position-small" href="#" data-uk-slidenav-next
                    data-uk-slideshow-item="next"></a></div>
        </div>
    </div>

    <section class="section-find uk-section-large uk-background-muted">
        <div class="uk-container">
            <div class="uk-card uk-card-default uk-card-body uk-box-shadow-large uk-border-rounded">
                <div class="find-box">
                    <div class="find-box__title uk-text-center uk-h2 uk-margin-medium-bottom">
                        <h2><span>{{ __('home.find_equipment_title') }}</span></h2>
                    </div>

                    <div class="find-box__form">
                        <form action="{{ route('home') }}" method="GET">
                            <div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-3@m" data-uk-grid>
                                <div>
                                    <label class="uk-form-label"
                                        for="filter-category">{{ __('home.filter_category') }}</label>
                                    <div class="uk-form-controls uk-inline uk-width-1-1">
                                        <select class="uk-select uk-form-large" id="filter-category" name="category">
                                            <option value="">{{ __('home.select_category') }}</option>
                                            @foreach ($equipmentCategories->whereNull('parent_id') as $parentCategory)
                                                <option value="{{ $parentCategory->id }}"
                                                    {{ isset($categoryId) && $categoryId == $parentCategory->id ? 'selected' : '' }}>
                                                    {{ $parentCategory->category_name }}
                                                </option>
                                                @foreach ($parentCategory->children->where('is_active', true) as $subCategory)
                                                    <option value="{{ $subCategory->id }}"
                                                        {{ isset($categoryId) && $categoryId == $subCategory->id ? 'selected' : '' }}>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;— {{ $subCategory->category_name }}
                                                    </option>
                                                @endforeach
                                            @endforeach
                                        </select>
                                        <span class="uk-form-icon uk-form-icon-flip"
                                            data-uk-icon="icon: chevron-down"></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="uk-form-label"
                                        for="filter-location">{{ __('home.filter_location') }}</label>
                                    <div class="uk-form-controls uk-inline uk-width-1-1">
                                        <select class="uk-select uk-form-large" id="filter-location" name="location">
                                            <option value="">{{ __('home.select_location') }}</option>
                                            @foreach ($locations as $loc)
                                                <option value="{{ $loc }}"
                                                    {{ isset($location) && $location == $loc ? 'selected' : '' }}>
                                                    {{ $loc }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="uk-form-icon uk-form-icon-flip" data-uk-icon="icon: location"></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="uk-form-label"
                                        for="filter-query">{{ __('home.filter_equipment_name') }}</label>
                                    <div class="uk-form-controls uk-inline uk-width-1-1">
                                        <input class="uk-input uk-form-large" id="filter-query" type="text"
                                            name="query" placeholder="{{ __('home.search_placeholder') }}"
                                            value="{{ $query ?? '' }}">
                                        <span class="uk-form-icon uk-form-icon-flip" data-uk-icon="icon: search"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-grid uk-grid-medium uk-child-width-1-1 uk-child-width-1-2@m uk-margin-top"
                                data-uk-grid>
                                <div>
                                    <label class="uk-form-label"
                                        for="filter-min-rate">{{ __('home.filter_min_rate') }}</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-large" id="filter-min-rate" type="number"
                                            step="0.01" name="min_daily_rate"
                                            placeholder="{{ __('home.min_rate_placeholder') }}"
                                            value="{{ $minDailyRate ?? '' }}">
                                    </div>
                                </div>
                                <div>
                                    <label class="uk-form-label"
                                        for="filter-max-rate">{{ __('home.filter_max_rate') }}</label>
                                    <div class="uk-form-controls">
                                        <input class="uk-input uk-form-large" id="filter-max-rate" type="number"
                                            step="0.01" name="max_daily_rate"
                                            placeholder="{{ __('home.max_rate_placeholder') }}"
                                            value="{{ $maxDailyRate ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="uk-margin-large-top uk-text-center">
                                <button type="submit"
                                    class="uk-button uk-button-primary uk-button-large uk-margin-small-right">
                                    <span>{{ __('home.search_button') }}</span>
                                </button>
                                <a href="{{ route('equipments') }}" class="uk-button uk-button-default uk-button-large">
                                    <span>{{ __('home.reset_filters') }}</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($hasSearch)
        <div class="uk-section-large uk-container">
            <div class="uk-text-center uk-margin-large-bottom">
                <h2 class="uk-h2">{{ __('home.search_results') }}</h2>
                @if ($searchResults->isEmpty())
                    <div class="uk-alert-warning" data-uk-alert>
                        <p>{{ __('home.no_results_found') }}</p>
                    </div>
                @else
                    <p class="uk-text-muted">{{ __('home.results_found', ['count' => $searchResults->total()]) }}</p>
                @endif
            </div>


            @if ($searchResults->isNotEmpty())
                <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                    @foreach ($searchResults as $equipment)
                        <div>
                            <div class="blog-item">
                                <div class="blog-item__media">
                                    <a href="{{ route('equipments.show', $equipment->id) }}">
                                        @if ($equipment->images->count() > 0)
                                            <img src="{{ asset('storage/' . $equipment->images->first()->image_url) }}"
                                                alt="{{ $equipment->name }}" data-uk-img>
                                        @else
                                            <img src="{{ asset('assets/home/img/equipment-default.jpg') }}"
                                                alt="صورة افتراضية" data-uk-img>
                                        @endif
                                    </a>
                                    <div class="blog-item__category">
                                        {{ $equipment->category->category_name ?? 'غير مصنف' }}</div>
                                </div>
                                <div class="blog-item__body">
                                    <div class="blog-item__info">
                                        <div class="blog-item__date">{{ $equipment->created_at->format('d M, Y') }}</div>
                                        <div class="blog-item__author">بواسطة <a
                                                href="#">{{ $equipment->owner->name ?? 'غير معروف' }}</a></div>
                                    </div>
                                    <div class="blog-item__title">{{ $equipment->name }}</div>
                                    <div class="blog-item__intro">{{ Str::limit($equipment->description, 100) }}</div>
                                </div>
                                <div class="blog-item__bottom">
                                    <a class="link-more" href="{{ route('equipments.show', $equipment->id) }}">
                                        <span>إقرأ أكثر</span>
                                        <img class="ukos" src="{{ asset('assets/home/img/icons/arrow.svg') }}"
                                            alt="arrow" data-uk-svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="uk-margin-large-top uk-text-center">
                    {{ $searchResults->appends(request()->query())->links() }}
                </div>
            @endif

        </div>
    @endif

    <div class="section-category uk-section-large">
        <div class="uk-container">
            <div class="section-title uk-text-center">
                <div class="uk-text-meta">{{ __('home.category_meta') }}</div>
                <div class="uk-h2">{{ __('home.category_title') }}</div>
            </div>
            <div class="section-content">
                <div class="uk-grid uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                    @forelse ($featuredCategories as $category)
                        <div>
                            <div class="category-item">
                                <a class="category-item__link uk-inline-clip uk-transition-toggle"
                                    href="{{ route('categories', ['parent_id' => $category->id]) }}" tabindex="0">
                                    <div class="category-item__media">
                                        <img src="{{ $category->image_url ? asset('storage/' . $category->image_url) : asset('home/assets/img/category-default.jpg') }}"
                                            alt="{{ $category->category_name }}" />
                                        <div class="uk-transition-fade">
                                            <div class="uk-overlay-primary uk-position-cover"></div>
                                            <div class="uk-position-center"><span
                                                    data-uk-icon="icon: plus; ratio: 2"></span></div>
                                        </div>
                                    </div>
                                    <div class="category-item__title"> <span>{{ $category->category_name }}</span></div>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="uk-width-1-1 uk-text-center">
                            <p>{{ __('home.category_empty') }}</p>
                        </div>
                    @endforelse
                </div>
                <div class="uk-margin-large-top uk-text-center">
                    <a class="uk-button uk-button-default uk-button-large" href="{{ route('categories') }}">
                        <span>{{ __('home.category_more') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="section-about">
        <div class="uk-container">
            <div class="section-content">
                <div class="uk-grid uk-grid-large uk-child-width-1-2@m" data-uk-grid>
                    <div><img src="{{ asset('assets/home/img/img-about.png') }}" alt="{{ __('home.about_meta') }}">
                    </div>
                    <div>
                        <div class="section-title">
                            <div class="uk-text-meta">{{ __('home.about_meta') }}</div>
                            <div class="uk-h2">{!! __('home.about_title') !!}</div>
                        </div>
                        <p>{{ $siteDescription }}</p>
                        <ul class="about-list">
                            <li>
                                <strong>{!! __('home.about_equipment_cost') !!}</strong>
                                <span>{{ __('home.about_equipment_desc') }}</span>
                            </li>
                            <li>
                                <strong>{!! __('home.about_customer_service') !!}</strong>
                                <span>{{ __('home.about_customer_desc') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
