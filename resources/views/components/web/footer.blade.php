<footer class="bg-gray-900 text-gray-300 py-8 mt-16 border-t-4 border-primary">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            {{-- قسم عن المتجر --}}
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">متجر الكتب الفاخر</h4>
                <p class="text-sm">وجهتك الأولى لأفضل الكتب الإلكترونية العربية والعالمية.</p>
                {{-- يمكن إضافة أيقونات التواصل الاجتماعي هنا --}}
            </div>
            {{-- قسم روابط سريعة --}}
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">روابط سريعة</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-primary transition">عن المتجر</a></li>
                    <li><a href="#" class="hover:text-primary transition">سياسة الخصوصية</a></li>
                    <li><a href="#" class="hover:text-primary transition">شروط الاستخدام</a></li>
                    <li><a href="#" class="hover:text-primary transition">تواصل معنا</a></li>
                </ul>
            </div>
            {{-- قسم النشرة البريدية --}}
            <div>
                <h4 class="text-lg font-semibold text-white mb-3">اشترك بالنشرة البريدية</h4>
                <form action="#" method="post">
                    <input type="email" placeholder="بريدك الإلكتروني"
                        class="w-full p-2 rounded bg-gray-800 border border-gray-700 text-sm mb-2 focus:outline-none focus:border-primary text-white">
                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white py-2 rounded text-sm font-semibold transition">اشتراك</button>
                </form>
            </div>
        </div>
        <div class="border-t border-gray-700 pt-6 text-center text-sm">
            <p>&copy; 2025 متجر الكتب الفاخر. جميع الحقوق محفوظة.</p> {{-- تم تحديث السنة --}}
        </div>
    </div>
</footer>
