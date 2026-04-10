@extends('layouts.master')

@section('title', __('home.about_title'))

@section('content')
<div class="section-slideshow">
    <div class="uk-position-relative uk-light" tabindex="-1"
        data-uk-slideshow="animation: fade; min-height: 400; max-height: 700;">
        <ul class="uk-slideshow-items">
            <li class="slideshow-item">
                <img src="{{ asset('assets/home/img/slideshow-1.jpg') }}" alt data-uk-cover>
                <div class="slideshow-item__content">
                    <div class="uk-position-center uk-position-small uk-text-center">
                        <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                            <span></span><span></span><span></span>
                        </div>
                        <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">
                            {{ __('home.slideshow_about') }}
                        </p>
                        <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">
                            {{ __('home.slideshow_title1') }}
                        </h2>
                    </div>
                </div>
            </li>
            <li class="slideshow-item">
                <img src="{{ asset('assets/home/img/slideshow-2.jpg') }}" alt data-uk-cover>
                <div class="slideshow-item__content">
                    <div class="uk-position-center uk-position-small uk-text-center">
                        <div class="header-icons" data-uk-slideshow-parallax="x: -200,200">
                            <span></span><span></span><span></span>
                        </div>
                        <p class="slideshow-item__desc" data-uk-slideshow-parallax="x: 200,-200">
                            {{ __('home.slideshow_desc2') }}
                        </p>
                        <h2 class="slideshow-item__title" data-uk-slideshow-parallax="x: -300,300">
                            {{ __('home.slideshow_title2') }}
                        </h2>
                    </div>
                </div>
            </li>
        </ul>
        <div class="uk-visible@m">
            <a class="uk-position-center-left uk-position-small" href="#" data-uk-slidenav-previous data-uk-slideshow-item="previous"></a>
            <a class="uk-position-center-right uk-position-small" href="#" data-uk-slidenav-next data-uk-slideshow-item="next"></a>
        </div>
    </div>
</div>

<div class="section-steps">
    <div class="uk-container uk-container-large">
        <div class="section-title uk-text-center">
            <div class="uk-text-meta">{{ $stepsSubtitle }}</div>
            <div class="uk-h2">{{ $stepsTitle }}</div>
        </div>
        <div class="section-content">
            <div class="steps-list uk-position-relative" tabindex="-1" data-uk-slider="finite: true">
                <ul class="uk-slider-items uk-child-width-1-1 uk-child-width-1-2@s uk-child-width-1-3@m uk-child-width-1-4@l">
                    @foreach (range(1,4) as $i)
                        <li>
                            <div class="steps-item">
                                <div class="steps-item__box"
                                    data-uk-tooltip="title: {{ ${'step'.$i.'Tooltip'} }}; pos: bottom-center; animation: uk-animation-slide-bottom-small; duration: 500; pos: bottom">
                                    <div class="steps-item__icon">
                                        <img src="{{ asset(${'step'.$i.'Icon'}) }}" alt="icon-step">
                                    </div>
                                    <div class="steps-item__title"><b>{{ $i }}</b><span>{{ ${'step'.$i.'Title'} }}</span></div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="uk-hidden@l">
                    <ul class="uk-slider-nav uk-dotnav uk-flex-center uk-margin-medium-top"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-stats">
    <div class="uk-container uk-container-xlarge">
        <div class="uk-grid uk-child-width-1-3@l uk-child-width-1-2@s" data-uk-grid>
            <div>
                <div class="stats-item">
                    <div class="stats-item__header">
                        <div class="stats-item__numb">{{ $usersCount }}</div>
                        <div class="stats-item__icon">
                            <img src="{{ asset('assets/home/img/icons/ico-stats-2.svg') }}" data-uk-svg>
                        </div>
                    </div>
                    <div class="stats-item__title">{{ __('home.stats_users') }}</div>
                    <div class="stats-item__desc">{{ __('home.stats_users_desc') }}</div>
                </div>
            </div>
            <div>
                <div class="stats-item">
                    <div class="stats-item__header">
                        <div class="stats-item__numb">{{ $equipmentsCount }}</div>
                        <div class="stats-item__icon">
                            <img src="{{ asset('assets/home/img/icons/ico-stats-1.svg') }}" data-uk-svg>
                        </div>
                    </div>
                    <div class="stats-item__title">{{ __('home.stats_equipments') }}</div>
                    <div class="stats-item__desc">{{ __('home.stats_equipments_desc') }}</div>
                </div>
            </div>
            <div>
                <div class="stats-item">
                    <div class="stats-item__header">
                        <div class="stats-item__numb">{{ $bookingsCount }}</div>
                        <div class="stats-item__icon">
                            <img src="{{ asset('assets/home/img/icons/ico-stats-3.svg') }}" data-uk-svg>
                        </div>
                    </div>
                    <div class="stats-item__title">{{ __('home.stats_bookings') }}</div>
                    <div class="stats-item__desc">{{ __('home.stats_bookings_desc') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-why-choose-us">
    <div class="uk-container">
        <div class="uk-grid uk-grid-large uk-child-width-1-2@m uk-flex-middle" data-uk-grid>
            <div>
                <div class="blocks-icon uk-grid uk-grid-medium uk-child-width-1-2@s" data-uk-grid>
                    @foreach (range(1,4) as $i)
                        <div>
                            <div class="block-icon">
                                <a class="block-icon__link" href="">
                                    <div class="block-icon__box">
                                        <div class="block-icon__ico">
                                            <img src="{{ asset(${'whyBox'.$i.'Icon'}) }}" alt="block-icon">
                                        </div>
                                        <div class="block-icon__title">{{ ${'whyBox'.$i.'Title'} }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="section-title">
                    <div class="uk-text-meta">{{ $whySubtitle }}</div>
                    <div class="uk-h2">{{ $whyTitle }}</div>
                </div>
                <div class="section-content">
                    <p class="justify">{{ $whyText }}</p>
                    <ul class="list-checked">
                        <li>{{ __('home.why_step1') }}</li>
                        <li>{{ __('home.why_step2') }}</li>
                        <li>{{ __('home.why_step3') }}</li>
                        <li>{{ __('home.why_step4') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section-equipment">
    <div class="uk-container">
        <div class="equipment-box">
            <div class="equipment-box__media">
                <img src="{{ asset('assets/home/img/img-equipment.jpg') }}" alt="">
            </div>
            <div class="equipment-box__desc">
                <div class="equipment-box__title">{{ $ctaTitle }}</div>
                <div class="equipment-box__text">{{ $ctaText }}</div>
                <div class="equipment-box__btn">
                    <a class="uk-button uk-button-large"
                       href="{{ auth()->check() ? route('equipments.create') : route('login', ['redirect' => 'equipments.create']) }}">
                        <span>{{ __('home.start_now') }}</span>
                        <img class="makos" src="{{ asset('assets/home/img/icons/arrow.svg') }}" alt="arrow" data-uk-svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
@endsection 