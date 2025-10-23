<x-guest-layout>
    <x-slot:head>
        {{-- Bootstrap RTL CSS --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.rtl.min.css" integrity="sha384-CfCrinSRH2IR6a4e6fy2q6ioOX7O6Mtm1L9vRvFZ1trBncWmMePhzvafv7oIcWiW" crossorigin="anonymous">

        <style>
            body {
                background-color: hsl(0, 0%, 96%) !important; /* General background color for the page */
            }
            .py-5 {
                padding-top: 1rem !important;
            }
            .text-primary{
                font-size: 45px !important;
                color: #edb509 !important;
            }
            .form {
                display: flex;
                flex-direction: column;
                gap: 10px;
                width: 100% !important;
                padding:  10px 20px;
                border-radius: 20px;
                position: relative;
            }

            .title {
                font-size: 28px;
                color: #edb509;
                font-weight: 600;
                letter-spacing: -1px;
                position: relative;
                margin: 0 !important;
                display: flex;
                align-items: center;
                padding-right: 30px; /* Adjust for RTL icon */
            }

            .title::before,.title::after {
                position: absolute;
                content: "";
                height: 16px;
                width: 16px;
                border-radius: 50%;
                right: 0px; /* Position to the right for RTL */
                background-color: #edb509;
            }

            .title::before {
                width: 18px;
                height: 18px;
                background-color: #e6f32f;
            }

            .title::after {
                width: 18px;
                height: 18px;
                animation: pulse 1s linear infinite;
            }

            .message, .signin {
                color: rgba(39, 39, 39, 0.822);
                font-size: 15px;
                font-weight: bold;
                margin: 0 !important;
                text-align: center;
            }

            .signin {
                text-align: center;
            }

            .signin a {
                color: #edb509;
            }

            .signin a:hover {
                text-decoration: underline #edb509;
            }

            .flex {
                display: flex;
                width: 100%;
                gap: 25px;
                flex-wrap: wrap; /* Allow wrapping on smaller screens */
            }
            .flex > label {
                flex: 1 1 calc(50% - 12.5px); /* Two columns, responsive */
            }
             @media (max-width: 576px) { /* Adjust for very small screens */
                .flex > label {
                    flex: 1 1 100%; /* Single column */
                }
            }


            .form label {
                position: relative;
                width: 100%;
            }

            .form label .customInput {
                padding: 10px 10px 20px 0px !important; /* Adjust padding for RTL */
            }
            .form label .input {
                width: 100%;
                padding: 10px 10px 20px 0px; /* Adjust padding for RTL */
                outline: 0;
                border: 1px solid rgba(0, 0, 0, 0.397);
                border-radius: 10px;
                text-align: right; /* Text align for RTL input */
            }

            .form label .input + span {
                position: absolute;
                right: 10px; /* Position to the right for RTL */
                top: 15px;
                color: rgb(85, 84, 84);
                font-size: 1.2em;
                cursor: text;
                transition: 0.3s ease;
            }

            .form label .input:placeholder-shown + span {
                top: 15px;
                font-size: 0.9em;
            }

            .form label .input:focus + span,.form label .input:valid + span {
                top: 30px;
                font-size: 0.7em;
                font-weight: 600;
            }

            .form label .input:valid + span {
                color: green;
            }

            .submit {
                border: none;
                outline: none;
                background-color: #edb509;
                padding: 10px;
                border-radius: 10px;
                color: #fff;
                font-size: 18px;
                transition: .3s ease;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                margin: auto;
                width: 80%;
            }

            .submit:hover {
                background-color: #d1a112;
                font-weight: bold;
            }

            @keyframes pulse {
                from {
                    transform: scale(0.9);
                    opacity: 1;
                }
                to {
                    transform: scale(1.8);
                    opacity: 0;
                }
            }

            /* Custom styling for Breeze input-error */
            .x-input-error {
                color: #ef4444; /* Tailwind red-500 */
                font-size: 0.875rem; /* text-sm */
                margin-top: 0.25rem; /* mt-1 */
                text-align: right;
                display: block;
            }
        </style>
    </x-slot:head>

    <!-- Section: Design Block -->
    <section class="">
        <!-- Jumbotron -->
        <div class="px-4 py-5 px-md-5 text-center text-lg-start" style="background-color: hsl(0, 0%, 96%)">
            <div class="container">
                <div class="row gx-lg-5 align-items-center">
                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <h1 class="my-5 display-3 fw-bold ls-tight">
                            العرض الأفضل لك
                            <br />
                            <span class="text-primary">لتأجير و إستئجار المعدات</span>
                        </h1>
                        <p style="color: hsl(217, 10%, 50.8%)">
                            بعد إتمام عملية التسجيل يمكنك تصفح المعدات و يمكنك ايضاً تأجير معداتك او إستئجار معدات من الموقع بأفضل الأسعار و بأسهل الطرق  .
                        </p>
                    </div>

                    <div class="col-lg-6 mb-5 mb-lg-0">
                        <div class="card bg-white shadow-lg rounded-4 p-4"> {{-- Added Breeze-like card styling --}}
                            <form class="form" method="POST" action="{{ route('register') }}">
                                @csrf
                                <p class="title">تسجيل جديد </p>
                                <p class="message">سجل الآن واحصل على إمكانية الوصول الكامل إلى منصتنا . </p>

                                <div class="flex">
                                    <label>
                                        <input required="" placeholder="" type="text" class="input customInput" name="first_name" value="{{ old('first_name') }}">
                                        <span>الإسم الأول </span>
                                    </label>
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2 x-input-error" />

                                    <label>
                                        <input required="" placeholder="" type="text" class="input customInput" name="last_name" value="{{ old('last_name') }}">
                                        <span>الإسم الأخير</span>
                                    </label>
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2 x-input-error" />
                                </div>

                                <label>
                                    <input  required="" placeholder="" type="email" class="input" name="email" value="{{ old('email') }}">
                                    <span>الإيميل</span>
                                </label>
                                <x-input-error :messages="$errors->get('email')" class="mt-2 x-input-error" />

                                <label>
                                    <input  required="" placeholder="" type="tel" class="input" name="phone_number" value="{{ old('phone_number') }}">
                                    <span>الهاتف</span>
                                </label>
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2 x-input-error" />

                                <label>
                                    <input  placeholder="" type="text" class="input" name="location_text" value="{{ old('location_text') }}">
                                    <span>العنوان</span>
                                </label>
                                <x-input-error :messages="$errors->get('location_text')" class="mt-2 x-input-error" />

                                <label>
                                    <input  placeholder="" type="text" class="input" name="description" value="{{ old('description') }}">
                                    <span>الوصف</span>
                                </label>
                                <x-input-error :messages="$errors->get('description')" class="mt-2 x-input-error" />

                                <label>
                                    <input required="" placeholder="" type="password" class="input" name="password" autocomplete="new-password">
                                    <span>كلمة المرور</span>
                                </label>
                                <x-input-error :messages="$errors->get('password')" class="mt-2 x-input-error" />

                                <label>
                                    <input required="" placeholder="" type="password" class="input" name="password_confirmation" autocomplete="new-password">
                                    <span>تأكيد كلمة المرور</span>
                                </label>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 x-input-error" />

                                <button type="submit" class="submit">تسجيل</button>
                                <p class="signin">هل لديك حساب ? <a href="{{ route('login') }}">تسجيل الدخول</a> </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Jumbotron -->
    </section>
    <!-- Section: Design Block -->

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        {{-- If you need these specific Bootstrap JS files, otherwise bundle.min.js is usually enough --}}
        {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script> --}}
    </x-slot:scripts>
</x-guest-layout>