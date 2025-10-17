@extends('layouts.master')

@section('title', $currentParent ? $currentParent->category_name : 'الفئات')

@section('content')

    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_categories.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">
                        {{ $currentParent ? $currentParent->category_name : 'فئات الإيجار' }}
                    </div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                            @if ($currentParent)
                                <li>
                                    <a href="{{ route('categories') }}">فئات الإيجار</a>
                                </li>
                                <li><span>{{ $currentParent->category_name }}</span></li>
                            @else
                                <li><span>فئات الإيجار</span></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="uk-section-large uk-container">
            {{-- إذا في فئة أب --}}
            @if ($currentParent)
                <div class="uk-margin-bottom">
                    <a href="{{ route('categories') }}" class="uk-button uk-button-default">
                        <span data-uk-icon="arrow-right"></span> الرجوع إلى الفئات الرئيسية
                    </a>
                </div>
            @endif

            <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                @forelse ($categories as $category)
                    <div>
                        <div class="category-item">
                            <a class="category-item__link uk-inline-clip uk-transition-toggle"
                               href="{{ $category->children->count() ? route('categories', ['parent_id' => $category->id]) : route('equipments', ['category' => $category->id]) }}"
                               tabindex="0">
                                <div class="category-item__media">
                                    <img src="{{ asset('assets/home/img/' . $category->image_url) }}"
                                         alt="{{ $category->category_name }}" />

                                    <div class="uk-transition-fade">
                                        <div class="uk-overlay-primary uk-position-cover"></div>
                                        <div class="uk-position-center">
                                            <span data-uk-icon="icon: {{ $category->children->count() ? 'folder' : 'plus' }}; ratio: 2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="category-item__title">
                                    <span>{{ $category->category_name }}</span>
                                    @if ($category->children->count())
                                        <small class="uk-text-meta d-block">({{ $category->children->count() }} قسم فرعي)</small>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="uk-width-1-1 uk-text-center">
                        <p>لا توجد فئات فرعية في هذا القسم.</p>
                    </div>
                @endforelse
            </div>

            <div class="uk-margin-top uk-text-center">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

@endsection
