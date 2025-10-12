@extends('layouts.master')

@section('title', 'الفئات')

@section('content')

    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_categories.jpg') }}">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title"> فئات الإيجار</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                            <li><span>شاشة الفئات</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="uk-section-large uk-container">
            <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                @foreach ($categories as $category)
                    <div>
                        <div class="category-item"> <a class="category-item__link uk-inline-clip uk-transition-toggle"
                                href="{{ route('equipments', ['category' => $category->id]) }}" tabindex="0">
                                <div class="category-item__media">
                                    <img src="{{ asset('assets/home/img/'.$category->image_url) }}" alt="{{ $category->category_name }}" />

                                    <div class="uk-transition-fade">
                                        <div class="uk-overlay-primary uk-position-cover"></div>
                                        <div class="uk-position-center"><span data-uk-icon="icon: plus; ratio: 2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="category-item__title">
                                    <span>{{ $category->category_name }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection
