<x-guest-layout>
    {{-- Custom CSS, FontAwesome, Bootstrap CSS for this page --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styles from your new template, adjusted for better integration */
        body {
            /* Ensure body has no extra margin/padding from Breeze or defaults */
            margin: 0;
            padding: 0;
            background-color: white !important;
            /* Force white background */
        }

        .vh-100 {
            min-height: 100vh;
        }

        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .h-custom {
            height: calc(100% - 73px);
        }

        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }

        /* *************** */
        .col-md-8 h2 {
            text-align: center;
            margin-top: 15px;
            color: #edb509;
        }

        .form {
            display: flex;
            flex-direction: column;
            margin: auto;
            gap: 10px;
            background-color: #ffffff;
            padding: 30px;
            width: 450px;
            /* Adjusted to match screenshot width */
            border-radius: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            /* Added shadow to match screenshot */
        }

        /* تنسيق الفاصل "أو" */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #888;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }

        .divider:not(:empty)::before {
            margin-left: .5em;
        }

        .divider:not(:empty)::after {
            margin-right: .5em;
        }

        /* تنسيق زر جوجل */
        .btn-google {
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            height: 50px;
            background-color: #ffffff;
            border: 1px solid #ecedec;
            border-radius: 10px;
            text-decoration: none !important;
            color: #2d2d2d;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        .btn-google:hover {
            background-color: #f8f9fa;
            border-color: #ddd;
            color: #2d2d2d;
        }

        .btn-google img {
            width: 20px;
            height: 20px;
            object-fit: contain;
            flex-shrink: 0;
            /* يمنع الصورة من الانضغاط */
        }

        @media (max-width: 450px) {
            .form {
                width: 100% !important;
            }
        }

        ::placeholder {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        .form button {
            align-self: flex-end;
        }

        .flex-column>label {
            color: #151717;
            font-weight: 600;
        }

        .inputForm {
            border: 1.5px solid #ecedec;
            border-radius: 10px;
            height: 50px;
            display: flex;
            align-items: center;
            padding-right: 10px;
            /* For RTL text alignment */
            padding-left: 10px;
            /* For the icon */
            transition: 0.2s ease-in-out;
        }

        /* Adjusted icon and input positioning for RTL */
        .inputForm svg,
        .inputForm i {
            margin-left: 10px;
            /* Space after icon */
            margin-right: 0;
        }

        .inputForm .input {
            margin-right: 0;
            margin-left: 10px;
            /* Space after input text before icon/button */
            text-align: right;
            /* Align input text to the right */
        }

        .inputForm .input::placeholder {
            text-align: right;
        }


        .input {
            border-radius: 10px;
            border: none;
            width: 85%;
            height: 100%;
        }

        .py-2 {
            text-align: justify;
        }

        .input:focus {
            outline: none;
        }

        .inputForm:focus-within {
            border: 1.5px solid #edb509;
        }

        .flex-row {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
        }

        .flex-row>div>label {
            font-size: 14px;
            color: black;
            font-weight: 400;
        }

        .span {
            font-size: 14px;
            margin-right: 5px;
            /* Adjust for RTL */
            color: #edb509;
            font-weight: 500;
            cursor: pointer;
        }

        .flex-row>div>input[type="checkbox"] {
            /* Style for checkbox */
            margin-left: 8px;
            /* Space between checkbox and label */
        }

        .button-submit {
            margin: 20px 0 10px 0;
            background-color: #edb509;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 500;
            border-radius: 10px;
            height: 50px;
            width: 100%;
            cursor: pointer;
        }

        .button-submit:hover {
            background-color: #f1c339;
        }


        .p {
            text-align: center;
            color: black;
            font-size: 14px;
            margin: 5px 0;
        }

        .btn {
            margin-top: 10px;
            width: 100%;
            height: 50px;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 500;
            gap: 10px;
            border: 1px solid #ededef;
            background-color: white;
            cursor: pointer;
            transition: 0.2s ease-in-out;
        }

        .btn-primary {
            background-color: #2d79f3;
        }

        /* Adjustments for the image column and overall alignment */
        .img-column {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .img-column img {
            max-width: 100%;
            height: auto;
            display: block;
            /* Remove extra space below image */
        }

        /* Ensure the main container for the form and image uses full height */
        .container-fluid.h-custom {
            height: 100vh;
            /* Use full viewport height */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Make the row also take full height if needed */
        .container-fluid.h-custom>.row {
            height: 100%;
            align-items: center;
            /* Center vertically */
        }

        /* Small adjustment for error messages to match the input form styling */
        .x-input-error {
            color: #ef4444;
            /* Tailwind red-500 */
            font-size: 0.875rem;
            /* text-sm */
            margin-top: 0.5rem;
            /* mt-2 */
        }
    </style>

    <section class="vh-100 d-flex justify-content-center align-items-center bg-white" dir="rtl">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100 w-100">
                <div class="col-md-9 col-lg-6 col-xl-5 img-column">
                    {{-- Replace 'login.jpeg' with your actual image file, e.g., 'lock_image.png' --}}
                    <img src="{{ asset('assets/home/img/login.jpeg') }}" class="img-fluid" alt="Login image">
                </div>
                <div class="res col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <h2 class="text-center mt-3" style="color: #edb509;">تسجيل الدخول</h2>

                    {{-- Session Status - place it where it makes sense in your design, e.g., above the form --}}
                    <x-auth-session-status class="mb-4 text-center" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="form">
                        @csrf

                        <!-- Email Address -->
                        <div class="flex-column">
                            <label for="email">الإيميل </label>
                        </div>
                        <div class="inputForm">
                            <input type="email" id="email" class="input" placeholder="أدخل الإيميل"
                                name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                            <svg height="20" viewBox="0 0 32 32" width="20" xmlns="http://www.w3.org/2000/svg">
                                <g id="Layer_3" data-name="Layer 3">
                                    <path
                                        d="m30.853 13.87a15 15 0 0 0 -29.729 4.082 15.1 15.1 0 0 0 12.876 12.918 15.6 15.6 0 0 0 2.016.13 14.85 14.85 0 0 0 7.715-2.145 1 1 0 1 0 -1.031-1.711 13.007 13.007 0 1 1 5.458-6.529 2.149 2.149 0 0 1 -4.158-.759v-10.856a1 1 0 0 0 -2 0v1.726a8 8 0 1 0 .2 10.325 4.135 4.135 0 0 0 7.83.274 15.2 15.2 0 0 0 .823-7.455zm-14.853 8.13a6 6 0 1 1 6-6 6.006 6.006 0 0 1 -6 6z">
                                    </path>
                                </g>
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 x-input-error" />

                        <!-- Password -->
                        <div class="flex-column mt-4">
                            <label for="passwordInput">كلمة المرور </label>
                        </div>
                        <div class="inputForm">
                            <input type="password" id="passwordInput" class="input" placeholder="أدخل كلمة المرور"
                                name="password" required autocomplete="current-password" />
                            <i id="togglePassword" class="fa-solid fa-eye" style="cursor:pointer;"></i>
                            <svg height="20" viewBox="-64 0 512 512" width="20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="m336 512h-288c-26.453125 0-48-21.523438-48-48v-224c0-26.476562 21.546875-48 48-48h288c26.453125 0 48 21.523438 48 48v224c0 26.476562-21.546875 48-48 48zm-288-288c-8.8125 0-16 7.167969-16 16v224c0 8.832031 7.1875 16 16 16h288c8.8125 0 16-7.167969 16-16v-224c0-8.832031-7.1875-16-16-16zm0 0">
                                </path>
                                <path
                                    d="m304 224c-8.832031 0-16-7.167969-16-16v-80c0-52.929688-43.070312-96-96-96s-96 43.070312-96 96v80c0 8.832031-7.167969 16-16 16s-16-7.167969-16-16v-80c0-70.59375 57.40625-128 128-128s128 57.40625 128 128v80c0 8.832031-7.167969 16-16 16zm0 0">
                                </path>
                            </svg>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 x-input-error" />

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex-row mt-4">
                            <div>
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <label for="remember_me" class="ms-2 text-sm text-gray-600">{{ __('تذكرني') }}</label>
                            </div>

                            @if (Route::has('password.request'))
                                <a class="span" href="{{ route('password.request') }}">
                                    {{ __('هل نسيت كلمة المرور ؟') }}
                                </a>
                            @endif
                        </div>
                        <!-- زر تسجيل الدخول العادي -->
                        <button type="submit" class="button-submit">{{ __('تسجيل الدخول') }}</button>

                        <!-- الفاصل (أو) -->
                        <div class="divider">أو</div>
                        <!-- زر جوجل (موجود مسبقاً) -->
                        <a href="{{ route('google.login') }}" class="btn-google mb-2">
                            <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" alt="Google">
                            <span>تسجيل الدخول بواسطة جوجل</span>
                        </a>

                        <!-- زر فيسبوك الجديد -->
                        <a href="{{ route('facebook.login') }}" class="btn-google"
                            style="background-color: #1877F2; color: white; border: none;">
                            <i class="fa-brands fa-facebook" style="font-size: 20px;"></i>
                            <span>تسجيل الدخول بواسطة فيسبوك</span>
                        </a>
                        <p class="p mt-3">ليس لديك حساب ؟
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="span">إنشاء حساب</a>
                            @endif
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Custom Body Scripts (e.g., Bootstrap JS, password toggle JS) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pwd = document.getElementById('passwordInput');
            const toggle = document.getElementById('togglePassword');

            if (pwd && toggle) {
                toggle.addEventListener('click', () => {
                    const isPassword = pwd.type === 'password';
                    pwd.type = isPassword ? 'text' : 'password';

                    toggle.classList.toggle('fa-eye');
                    toggle.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</x-guest-layout>
