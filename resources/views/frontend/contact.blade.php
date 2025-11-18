@extends('layouts.master')

@section('title', 'تواصل معنا')

@section('content')
    <div class="page-head">
        <div class="page-head__bg" style="background-image: url({{ asset('assets/home/img/bg/bg_contacts.jpg') }})">
            <div class="page-head__content" data-uk-parallax="y: 0, 100">
                <div class="uk-container">
                    <div class="header-icons"><span></span><span></span><span></span></div>
                    <div class="page-head__title">تواصل معنا</div>
                    <div class="page-head__breadcrumb">
                        <ul class="uk-breadcrumb">
                            <li><a href="{{ route('home') }}">الصفحة الرئيسية</a></li>
                            <li><span>تواصل معنا</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-content">
        <div class="uk-section-large uk-container">
            <div class="contacts-block">
                <div class="uk-grid uk-grid-collapse" data-uk-grid>
                    <div class="uk-width-1-3@m">
                        <div class="sidebar">
                            <div class="widjet widjet-contacts">
                                <h4 class="widjet__title">تفاصيل الاتصال</h4>
                                <ul class="contacts-list">
                                    <li class="contacts-list-item">
                                        <div class="contacts-list-item__icon"><img src="{{ asset($iconAddress) }}"
                                                data-uk-svg alt="HeadOffice Address"></div>
                                        <div class="contacts-list-item__desc">
                                            <div class="contacts-list-item__label">عنوان المكتب الرئيسي</div>
                                            <div class="contacts-list-item__content">{{ $contactAddress }}</div>
                                        </div>
                                    </li>
                                    <li class="contacts-list-item">
                                        <div class="contacts-list-item__icon"><img src="{{ asset($iconPhone) }}" data-uk-svg
                                                alt="For Rental Support"></div>
                                        <div class="contacts-list-item__desc">
                                            <div class="contacts-list-item__label">للدعم الفني</div>
                                            <div class="contacts-list-item__content">
                                                <a href="tel:{{ $contactPhone }}">{{ $contactPhone }}</a>
                                            </div>

                                        </div>
                                    </li>
                                    <li class="contacts-list-item">
                                        <div class="contacts-list-item__icon"><img src="{{ asset($iconHours) }}" data-uk-svg
                                                alt="The Office Hours"></div>
                                        <div class="contacts-list-item__desc">
                                            <div class="contacts-list-item__label">ساعات العمل</div>
                                            <div class="contacts-list-item__content">{{ $contactHours }}</div>
                                        </div>
                                    </li>
                                    <li class="contacts-list-item">
                                        <div class="contacts-list-item__icon"><img src="{{ asset($iconEmail) }}" data-uk-svg
                                                alt="Send Us Email">
                                        </div>
                                        <div class="contacts-list-item__desc">
                                            <div class="contacts-list-item__label">تواصل على الايميل</div>
                                            <div class="contacts-list-item__content">
                                                <a target="_blank"
                                                    href="https://mail.google.com/mail/?view=cm&to={{ $contactEmail }}">
                                                    {{ $contactEmail }}
                                                </a>
                                            </div>

                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-2-3@m">
                        <div class="block-form">
                            <div class="section-title">
                                <div class="uk-h2">إرسال رسالة</div>
                            </div>
                            <div class="section-content">
                                {{--  --}}
                                @if (session('success'))
                                    <div class="uk-alert-success" uk-alert>
                                        <p>{{ session('success') }}</p>
                                    </div>
                                @endif

                                @if (session('error'))
                                    <div class="uk-alert-danger" uk-alert>
                                        <p>{{ session('error') }}</p>
                                    </div>
                                @endif
                                {{--  --}}
                                <p>لن يتم نشر عنوان بريدك الإلكتروني.</p>
                                <form action="{{ route('contact') }}" method="POST">
                                    @csrf
                                    <div class="uk-grid uk-grid-small uk-child-width-1-2@s" data-uk-grid>
                                        <div>
                                            <input class="uk-input uk-form-large" type="text" name="name"
                                                placeholder="الإسم ">
                                        </div>
                                        <div>
                                            <input class="uk-input uk-form-large" type="text" name="email"
                                                placeholder="الإيميل ">
                                        </div>
                                        <div class="uk-width-1-1">
                                            <input class="uk-input uk-form-large" type="text" name="subject"
                                                placeholder="عنوان الموضوع">
                                        </div>
                                        <div class="uk-width-1-1">
                                            <textarea class="uk-textarea uk-form-large" name="content" placeholder="محتوى الرسالة"></textarea>
                                        </div>
                                        <div><button class="uk-button uk-button-large" type="submit"> <span>إرسال
                                                </span><img class="makos"
                                                    src="{{ asset('assets/home/img/icons/arrow.svg') }}" alt="arrow"
                                                    data-uk-svg></button></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
