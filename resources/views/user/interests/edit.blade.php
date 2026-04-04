@extends('layouts.master')

@section('title', 'اهتماماتي')

@section('content')

    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">اهتماماتي</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                            <li><span>اهتماماتي</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <div class="uk-section-large uk-container">

            <div class="uk-text-center uk-margin-large-bottom">
                <h2 class="uk-h2">اختر اهتماماتك </h2>
                <p class="uk-text-muted">سنعرض لك المعدات بناءً على ما تختاره</p>
            </div>

            @if(session('success'))
                <div class="uk-alert-success uk-margin-bottom" data-uk-alert>
                    <a class="uk-alert-close" data-uk-close></a>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('interests.update') }}">
                @csrf
                @method('PUT')

                <div class="uk-grid uk-grid-medium uk-child-width-1-2@s uk-child-width-1-3@m" data-uk-grid>
                    @foreach ($categories as $category)
                        <div>
                            <label class="uk-flex uk-flex-middle" style="cursor: pointer; gap: 10px;">
                                <input
                                    type="checkbox"
                                    class="uk-checkbox"
                                    name="interests[]"
                                    value="{{ $category->id }}"
                                    {{ in_array($category->id, $userInterests) ? 'checked' : '' }}
                                />
                                <span>{{ $category->category_name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="uk-text-center uk-margin-large-top">
                    <button type="submit" class="uk-button uk-button-primary uk-button-large">
                        حفظ الاهتمامات
                    </button>
                </div>

            </form>

        </div>
    </div>

@endsection