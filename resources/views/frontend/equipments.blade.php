@extends('layouts.master')

@section('title', 'نتائج البحث عن المعدات')

@section('content')
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">المعدات</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li> {{-- تم تصحيح الـ route --}}
                            <li><span>المعدات</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @auth
        @if (!$hasInterests)
            <div class="page-content">
                <div class="uk-section-large uk-container">
                    <div class="uk-text-center">
                        <h2 class="uk-h2">بناءً على اهتماماتك 🎯</h2>
                        <p class="uk-text-muted">لم تحدد أي اهتمامات بعد</p>
                        <a href="{{ route('interests.edit') }}"
                            class="uk-button uk-button-primary uk-button-large uk-margin-top">
                            <span data-uk-icon="icon: plus"></span>
                            إضافة اهتمامات
                        </a>
                    </div>
                </div>
            </div>
        @elseif ($recommendedEquipments->isNotEmpty())
            {{-- قسم الاهتمامات --}}
            @auth
                @if ($recommendedEquipments->isNotEmpty())
                    <div class="page-content">
                        <div class="uk-section-large uk-container">

                            <div class="uk-text-center uk-margin-large-bottom">
                                <h2 class="uk-h2">بناءً على اهتماماتك </h2>
                                <p class="uk-text-muted">معدات مختارة خصيصاً لك</p>
                            </div>

                            <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                                @foreach ($recommendedEquipments as $equipment)
                                    <div>
                                        <div class="blog-item">
                                            <div class="blog-item__media">
                                                <a href="{{ route('equipments.show', $equipment->id) }}">
                                                    @if ($equipment->images->count() > 0)
                                                        <img src="{{ asset('storage/' . $equipment->images->first()->image_url) }}"
                                                            alt="{{ $equipment->name }}" data-uk-img>
                                                    @else
                                                        <img src="{{ asset('assets/home/img/equipment-default.jpg') }}"
                                                            alt="صورة افتراضية للمعدة" data-uk-img>
                                                    @endif
                                                </a>
                                                <div class="blog-item__category">
                                                    {{ $equipment->category->category_name ?? 'غير مصنف' }}</div>
                                            </div>
                                            <div class="blog-item__body">
                                                <div class="blog-item__info">
                                                    <div class="blog-item__date">{{ $equipment->created_at->format('d M, Y') }}
                                                    </div>
                                                    <div class="blog-item__author">بواسطة <a
                                                            href="#">{{ $equipment->owner->name ?? 'غير معروف' }}</a></div>
                                                </div>
                                                <div class="blog-item__title">{{ $equipment->name }}</div>
                                                <div class="blog-item__intro">{{ Str::limit($equipment->description, 100) }}</div>
                                                <div class="rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <span class="uk-icon"
                                                            data-uk-icon="icon: star{{ $equipment->average_rating >= $i ? '' : '-o' }}; ratio: 0.8"></span>
                                                    @endfor
                                                </div>
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

                            {{-- رابط لتعديل الاهتمامات --}}
                            <div class="uk-text-center uk-margin-large-top">
                                <a href="{{ route('interests.edit') }}" class="uk-button uk-button-default">
                                    <span data-uk-icon="icon: settings"></span>
                                    تعديل اهتماماتك
                                </a>
                            </div>

                        </div>
                    </div>
                @endif
            @endauth
        @endif
    @endauth

@endsection
