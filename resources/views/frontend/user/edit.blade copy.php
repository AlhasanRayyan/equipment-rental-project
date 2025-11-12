<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <title> تحديث بيانات المستخدم</title>
    <meta content="Templines" name="author">
    <meta content="SPCER" name="description">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="HandheldFriendly" content="true">
    <meta name="format-detection" content="telephone=no">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/home/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('assets/home/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('assets/home/img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/home/img/favicon/site.html') }}">
    <meta name="msapplication-TileColor" content="#da532c">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .update-container {
            background-color: rgba(255, 255, 255, 0.97);
            border-radius: 25px;
            width: 90%;
            max-width: 1000px;
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
            gap: 40px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .photo-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .photo-section::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 193, 7, 0.3), transparent 60%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 0;
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.7;
            }

            50% {
                transform: translate(-50%, -50%) scale(1.1);
                opacity: 0.4;
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 0.7;
            }
        }

        .photo-section img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            border: 4px solid #FFC107;
            object-fit: cover;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
            transition: transform 0.3s;
        }

        .photo-section img:hover {
            transform: scale(1.1);
        }

        #fileInput {
            display: none;
        }

        .custom-file-btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(45deg, #FFC107, #FFB300);
            color: #fff;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .custom-file-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .file-name {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
            max-width: 200px;
            text-align: center;
            word-break: break-all;
        }

        .form-section {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-section h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
        }

        .form-group {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff8e1;
            border-radius: 15px;
            padding: 10px 15px;
            transition: all 0.3s;
            border: 1px solid #FFD54F;
        }

        .form-group .phonInput {
            text-align: right;
        }

        .form-group:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .form-group i {
            color: #FFC107;
            min-width: 25px;
            text-align: center;
            font-size: 18px;
        }

        .form-group input,
        .form-group textarea {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font-size: 16px;
            padding: 10px 5px;
            resize: none;
        }

        .form-group textarea {
            padding-top: 35PX;
        }

        .update-btn {
            padding: 15px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(45deg, #FFC107, #FFB300);
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .update-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
        }

        @media(max-width:900px) {
            .update-container {
                flex-direction: column;
                align-items: center;
            }

            .form-section {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="update-container">
            {{-- صورة المستخدم --}}
            <div class="photo-section">
                <img src="{{ $user && $user->profile_picture_url
                    ? asset('storage/' . $user->profile_picture_url)
                    : asset('assets/home/img/default-user.png') }}"
                    id="userPhoto" alt="صورة المستخدم">

                <label class="custom-file-btn" for="fileInput">اختر صورة</label>
                <input type="file" id="fileInput" name="profile_image" accept="image/*"
                    onchange="previewFile(event)">
                <div class="file-name" id="fileName">لم يتم اختيار أي ملف</div>
            </div>

            {{-- حقول المعلومات --}}
            <div class="form-section">
                <h2>تحديث بيانات المستخدم</h2>

                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="first_name" id="firstName" value="{{ $user->first_name }}"
                        placeholder="الاسم الأول">
                </div>

                <div class="form-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="last_name" id="lastName" value="{{ $user->last_name }}"
                        placeholder="الاسم الأخير">
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" value="{{ $user->email }}"
                        placeholder="البريد الإلكتروني">
                </div>

                <div class="form-group">
                    <i class="fas fa-phone"></i>
                    <input class="phonInput" name="phone_number" type="tel" id="phone"
                        value="{{ $user->phone_number }}" placeholder="رقم الهاتف">
                </div>

                <div class="form-group">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location_text" id="address" value="{{ $user->location_text }}"
                        placeholder="العنوان">
                </div>

                <div class="form-group">
                    <i class="fas fa-align-left"></i>
                    <textarea name="description" id="bio" placeholder="الوصف / ملاحظات إضافية">{{ $user->description }}</textarea>
                </div>

                <button class="update-btn" type="submit">تحديث البيانات</button>
            </div>
        </div>
    </form>

    {{-- سكريبت لمعاينة الصورة قبل الرفع --}}
    {{-- مش شغال كنه --}}
    {{-- <script>
        function previewFile(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            if (file) {
                reader.onload = function(e) {
                    document.getElementById('userPhoto').src = e.target.result;
                    document.getElementById('fileName').textContent = file.name;
                };
                reader.readAsDataURL(file);
            } else {
                document.getElementById('fileName').textContent = "لم يتم اختيار أي ملف";
            }
        }
    </script> --}}


    <script>
        function previewFile(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('userPhoto').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);

            const fileInput = document.getElementById('fileInput');
            const fileName = document.getElementById('fileName');
            fileName.textContent = fileInput.files[0].name;
        }
    </script>
</body>

</html>
