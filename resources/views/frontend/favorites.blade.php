@extends('layouts.master')

@section('title', 'المفضلة') 

@section('content')
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_blog.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title"> المفضلة </div>
                </div>
            </div>
        </div>
    </div>
    <!-- start search par  -->
    <div class="section-find section-find1">
        <div class="uk-container">
            <div class="find-box">
                <div class="find-box__title"> <span>ابحث في المفضلة</span></div>
                <div class="find-box__form">
                    <form action="{{ route('favorites.index') }}" method="GET">
                        <div class="uk-grid uk-grid-medium uk-flex-middle uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                            <div>
                                <div class="uk-inline uk-width-1-1">
                                    <select class="uk-select uk-form-large" name="category">
                                        <option value="">إختر الصنف</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" @if(request('category') == $category->id) selected @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="uk-form-icon"><img src="{{ asset('assets/img/icons/truck.svg') }}" alt="truck" data-uk-svg></span>
                                </div>
                            </div>
                            <div>
                                <div class="uk-inline uk-width-1-1">
                                    <input class="uk-input uk-form-large uk-width-1-1" type="text" name="equipment_name" placeholder="اسم المعدة" value="{{ request('equipment_name') }}">
                                    <span class="uk-form-icon"><img src="{{ asset('assets/img/icons/derrick.svg') }}" alt="derrick" data-uk-svg></span>
                                </div>
                            </div>
                            <div>
                                <input class="main-input uk-input uk-form-large uk-width-1-1" type="submit" value="ابحث هنا">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- end search par    -->
    <div class="page-content">
        <div class="uk-section-large uk-container">
            @if(session('success'))
                <div class="uk-alert-success" data-uk-alert>
                    <a class="uk-alert-close" data-uk-close></a>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="uk-alert-danger" data-uk-alert>
                    <a class="uk-alert-close" data-uk-close></a>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($favorites->isEmpty())
                <p class="uk-text-center uk-text-large uk-margin-large-top">لا توجد معدات في المفضلة بعد.</p>
            @else
                <div class="uk-grid uk-grid-medium uk-child-width-1-3@m uk-child-width-1-2@s" data-uk-grid>
                    @foreach($favorites as $userFavorite)
                        @php
                            $equipment = $userFavorite->equipment;
                            // في حال تم حذف المعدة الأساسية، يمكن تخطيها
                            if (!$equipment) continue;
                        @endphp
                        <div>
                            <div class="blog-item">
                                <div class="blog-item__media">
                                    <a href="{{ route('equipments.show', $equipment->id) }}">
                                        {{-- عرض أول صورة للمعدة، أو صورة افتراضية إذا لم تكن هناك صور --}}
                                        <img src="{{ $equipment->images->first() ? asset('storage/' . $equipment->images->first()->image_path) : asset('assets/img/blog-placeholder.jpg') }}"
                                             alt="{{ $equipment->name }}">
                                    </a>
                                    <div class="blog-item__Favorites">
                                        {{-- نموذج لحذف المعدة من المفضلة عند إلغاء تحديد القلب --}}
                                        <form action="{{ route('favorites.destroy', $userFavorite->id) }}" method="POST" class="remove-favorite-form">
                                            @csrf
                                            @method('DELETE')
                                            {{-- ID يجب أن يكون فريدًا لكل عنصر في الحلقة --}}
<input type="checkbox" checked="checked" id="favorite_{{ $userFavorite->id }}" name="favorite-checkbox" value="favorite-button" style="display: none;">                                            <label for="favorite_{{ $userFavorite->id }}" class="containerrr">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-heart">
                                                    <path
                                                      d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                                                    </path>
                                                </svg>
                                            </label>
                                        </form>
                                    </div>
                                </div>
                                <div class="blog-item__body">
                                    <div class="blog-item__info">
                                        <div class="blog-item__date">{{ $userFavorite->created_at->format('d M Y') }}</div>
                                        <div class="blog-item__author"> بواسطة<a href="#">{{ $equipment->owner->name ?? 'غير معروف' }} </a></div>
                                    </div>
                                    <div class="blog-item__title">{{ $equipment->name }}</div>
                                    <div class="blog-item__intro">{{ Str::limit($equipment->description, 100) }}</div>
                                    <!-- start stars  -->
                                    <div class="rating">
                                        @php $avgRating = round($equipment->average_rating ?? 0); @endphp
                                        @for ($i = 5; $i >= 1; $i--)
                                            {{-- IDs للنجوم يجب أن تكون فريدة لكل معدة --}}
                                            <input value="{{ $i }}" name="rate_{{ $equipment->id }}" id="star{{ $i }}_{{ $equipment->id }}" type="radio" @if($avgRating == $i) checked="" @endif>
                                            <label title="text" for="star{{ $i }}_{{ $equipment->id }}"></label>
                                        @endfor
                                    </div>
                                    <!-- end stars  -->
                                </div>
                                <div class="blog-item__bottom">
                                    <a class="link-more" href="{{ route('equipments.show', $equipment->id) }}">
                                        <span>إقرأ أكثر </span>
                                        <img class="makos" src="{{ asset('assets/img/icons/arrow.svg') }}" alt="arrow" data-uk-svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
           
            @endif
        </div>
    </div>
@endsection

@push('scripts') {{-- لإضافة سكريبتات خاصة بهذه الصفحة --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.blog-item__Favorites input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    if (!this.checked) { // إذا تم إلغاء تحديد خانة الاختيار
                        const form = this.closest('form');
                        if (form) {
                            form.submit(); // إرسال النموذج لحذف المفضلة
                        }
                    }
                });
            });
        });
    </script>
@endpush