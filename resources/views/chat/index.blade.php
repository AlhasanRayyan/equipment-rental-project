@extends('layouts.master')

@section('title', 'الدردشات')

@push('styles')
    {{-- Custom styles for the chat page --}}
    <style>
        /* CSS الخاص بالشات هنا. أعدت ترتيب بعض الأشياء لتناسب الـ layout */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
        }

        .content {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .card {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: none; /* إزالة الحدود الافتراضية للكارد إذا أردت */
            border-radius: 0; /* إزالة حواف الكارد إذا أردت */
        }

        .row.g-0 {
            flex: 1;
            display: flex;
            overflow: hidden;
            height: 100%; /* يجب أن يكون 100% بالنسبة للعنصر الأب (card) */
        }

        .border-right {
            height: 100%; /* يجب أن يكون 100% بالنسبة للعنصر الأب (row) */
            overflow-y: auto;
            border-right: 1px solid #dee2e6 !important;
            background-color: #fff;
        }

        .float-right { /* هذا الـ float-right في الـ template الأصلي كان يُستخدم لعدم قراءة الرسائل،
                          وفي سياق RTL غالباً ما يكون المقصود به align-left لـ badge.
                          الـ float-right في RTL يعني إلى اليسار.
                          إذا كان الـ badge لونه أخضر ويظهر في يسار العنصر في RTL، فهو يعمل كما هو متوقع */
            float: left !important;
            color: #fff;
            margin-right: 10px; /* لإنشاء مسافة بين البادج والنص */
        }

        .chat-messages {
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: #fff;
            padding: 1rem; /* أضفت padding افتراضي */
            height: calc(100vh - 150px); /* يحسب تلقائيًا حسب الأدوات العليا والسفلى */
        }

        .p-4 {
            padding: 1rem !important;
        }

        /* رسائلي (يمين) ورسائل الطرف الآخر (يسار) */
        .chat-message-left,
        .chat-message-right {
            display: flex;
            flex-shrink: 0;
            margin-bottom: 15px;
        }

        .chat-message-left { /* رسائل الطرف الآخر */
            margin-right: auto;
        }

        .chat-message-right { /* رسائل المستخدم الحالي */
            flex-direction: row-reverse;
            margin-left: auto;
        }
        
        /* تلوين خلفيات الرسائل */
        .chat-message-right .flex-shrink-1 { /* خلفية رسائل المستخدم الحالي */
            background-color: rgb(246, 170, 0 ,41%) !important;
            border-radius: 20px !important;
        }
        .chat-message-left .flex-shrink-1 { /* خلفية رسائل الطرف الآخر */
            background-color: #e9ecef !important; /* لون افتراضي فاتح */
            border-radius: 20px !important;
        }


        .send {
            background: #fff;
            flex-shrink: 0;
        }

        @media (max-width: 992px) {
            .chat-list {
                display: block !important;
                /* على الموبايل، قائمة المحادثات تظهر أولاً */
            }

            .chat-list.hidden-on-mobile {
                display: none !important;
            }

            .chat-window {
                display: none !important;
            }

            .chat-window.show-on-mobile {
                display: flex !important;
                flex-direction: column;
            }

            /* .col-lg-8 { display: none; } هذا سيخفيها تماماً وليس فقط في الـ media query */

            .chat-messages {
                height: calc(100vh - 143px);
            }
        }


        /* تحسين مظهر السحب */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }

        .chat-online {
            color: green;
        }

        .send-btn {
            margin-left: 5px; /* يوضع الزر على يسار مربع الإدخال في RTL */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .send-btn i {
            margin: 0;
        }
    </style>
@endpush

@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="row g-0">
                    <!-- قائمة المحادثات -->
                    <div class="col-12 col-lg-4 col-xl-3 border-right chat-list" id="chatList">
                        <div class="px-4 d-block">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <input type="text" class="form-control my-3" placeholder="بحث .....">
                                </div>
                            </div>
                        </div>

                        <div id="conversations-list">
                            {{-- هنا سيتم تحميل قائمة المحادثات بواسطة JavaScript --}}
                            <div class="text-center text-muted p-3">جاري تحميل المحادثات...</div>
                        </div>

                        <hr class="d-block d-lg-none mt-1 mb-0">
                    </div>

                    <!-- نافذة الشات المحددة -->
                    <div class="col-12 col-lg-8 col-xl-9 chat-window" id="chatWindow">
                        <div class="py-2 px-4 border-bottom d-flex align-items-center" id="chat-header">
                            {{-- رأس المحادثة سيتم تحميله بواسطة JavaScript --}}
                            <i class="fas fa-arrow-right d-lg-none mr-2 back-arrow" onclick="backToList()"
                                style="cursor: pointer;"></i>
                            <div class="text-center text-muted p-3 flex-grow-1">اختر محادثة للبدء</div>
                        </div>

                        <div class="position-relative">
                            <div class="chat-messages p-4" id="messages-container">
                                {{-- هنا سيتم تحميل الرسائل بواسطة JavaScript --}}
                            </div>
                        </div>

                        <div class="flex-grow-0 py-3 px-4 send border-top" id="chat-input-area" style="display: none;">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="أكتب رسالة" id="message-input">
                                <button class="btn btn-primary send-btn" id="send-message-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    {{-- Custom scripts for the chat page --}}
    <script>
        // احصل على الرمز المميز (CSRF token) من meta tag
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'; // مهم لـ Laravel Sanctum

        // يجب أن يكون المستخدم الحالي مسجلاً الدخول (Auth::id() في الـ blade)
        const currentUserId = {{ Auth::id() }};
        let currentConversationId = null;
        let echoChannel = null; // لتخزين قناة Echo الحالية

        const chatListDiv = document.getElementById('chatList');
        const chatWindowDiv = document.getElementById('chatWindow');
        const conversationsListDiv = document.getElementById('conversations-list');
        const messagesContainer = document.getElementById('messages-container');
        const chatHeader = document.getElementById('chat-header');
        const messageInput = document.getElementById('message-input');
        const sendMessageBtn = document.getElementById('send-message-btn');
        const chatInputArea = document.getElementById('chat-input-area');

        // الدوال الأساسية لتشغيل الـ UI
        function openChat(conversationId, otherUserName, otherUserAvatar, event) {
            if (event) event.preventDefault(); // يمنع الانتقال الافتراضي للرابط

            // تحديث رأس المحادثة
            chatHeader.innerHTML = `
                <i class="fas fa-arrow-right d-lg-none mr-2 back-arrow" onclick="backToList()" style="cursor: pointer;"></i>
                <div class="position-relative">
                    <img src="${otherUserAvatar}" style="margin-left: 5px;" class="rounded-circle mr-1" alt="${otherUserName}" width="40" height="40">
                </div>
                <div class="flex-grow-1 pl-3 text-right">
                    <strong>${otherUserName}</strong>
                    <div class="text-muted small"><em></em></div>
                </div>
            `;
            chatInputArea.style.display = 'flex'; // إظهار حقل الإرسال

            // إخفاء قائمة المحادثات وإظهار نافذة الشات على الموبايل
            if (window.innerWidth < 992) {
                chatListDiv.classList.add('hidden-on-mobile');
                chatWindowDiv.classList.add('show-on-mobile');
            }

            // تحميل الرسائل للمحادثة المحددة
            loadMessages(conversationId);
        }

        function backToList() {
            if (window.innerWidth < 992) {
                chatListDiv.classList.remove('hidden-on-mobile');
                chatWindowDiv.classList.remove('show-on-mobile');
            }
            chatInputArea.style.display = 'none'; // إخفاء حقل الإرسال
            currentConversationId = null; // إفراغ الـ ID الحالي
            if (echoChannel) {
                echoChannel.leave(); // مغادرة قناة Echo السابقة
                echoChannel = null; // تفريغ القناة
            }
            messagesContainer.innerHTML = ''; // مسح الرسائل
            chatHeader.innerHTML = `<i class="fas fa-arrow-right d-lg-none mr-2 back-arrow" onclick="backToList()" style="cursor: pointer; display: none;"></i><div class="text-center text-muted p-3 flex-grow-1">اختر محادثة للبدء</div>`; // إعادة رأس المحادثة للوضع الافتراضي
        }

        // دالة لجلب وعرض قائمة المحادثات
        async function fetchConversations() {
            try {
                const response = await axios.get('/api/conversations');
                const conversations = response.data;
                conversationsListDiv.innerHTML = ''; // مسح المحتوى الحالي

                if (conversations.length === 0) {
                    conversationsListDiv.innerHTML = '<div class="text-center text-muted p-3">لا توجد محادثات.</div>';
                    return;
                }

                conversations.forEach(conv => {
                    const otherUser = conv.other_user; // استخدم الـ accessor
                    const lastMessage = conv.messages.length > 0 ? conv.messages[0].content : 'لا توجد رسائل';
                    const unreadCount = conv.unread_messages_count > 0 ? `<div class="badge bg-success float-right">${conv.unread_messages_count}</div>` : '';
                    const otherUserAvatar = otherUser.profile_picture_url || 'https://bootdey.com/img/Content/avatar/avatar7.png';
                    const otherUserName = otherUser.full_name || `${otherUser.first_name} ${otherUser.last_name}`; // للتأكد لو الـ accessor مش موجود


                    const conversationItem = `
                        <a href="#" class="list-group-item list-group-item-action border-0"
                            onclick="openChat(${conv.id}, '${otherUserName.replace(/'/g, "\\'")}', '${otherUserAvatar}', event)">
                            ${unreadCount}
                            <div class="d-flex align-items-start">
                                <img src="${otherUserAvatar}" style="margin-left: 5px;"
                                    class="rounded-circle mr-1" alt="${otherUserName}" width="40" height="40">
                                <div class="flex-grow-1 ml-3 text-right">
                                    ${otherUserName}
                                    <div class="small text-muted">${lastMessage}</div>
                                </div>
                            </div>
                        </a>
                    `;
                    conversationsListDiv.innerHTML += conversationItem;
                });

            } catch (error) {
                console.error('Error fetching conversations:', error);
                conversationsListDiv.innerHTML = '<div class="text-center text-danger p-3">حدث خطأ أثناء تحميل المحادثات.</div>';
            }
        }

        // دالة لجلب وعرض رسائل محادثة محددة
        async function loadMessages(conversationId) {
            messagesContainer.innerHTML = '<div class="text-center text-muted p-3">جاري تحميل الرسائل...</div>';
            currentConversationId = conversationId;

            // مغادرة القناة السابقة والاشتراك في القناة الجديدة
            if (echoChannel) {
                echoChannel.leave();
            }
            echoChannel = window.Echo.private(`conversations.${currentConversationId}`)
                .listen('.message.sent', (e) => {
                    // فقط إذا كانت الرسالة ليست من المستخدم الحالي (لتجنب التكرار)
                    if (e.message.sender_id !== currentUserId) {
                        appendMessage(e.message);
                    }
                    fetchConversations(); // تحديث قائمة المحادثات عند وصول رسالة جديدة
                });


            try {
                const response = await axios.get(`/api/conversations/${conversationId}/messages`);
                const messages = response.data;
                messagesContainer.innerHTML = ''; // مسح المحتوى الحالي

                if (messages.length === 0) {
                    messagesContainer.innerHTML = '<div class="text-center text-muted p-3">لا توجد رسائل بعد.</div>';
                } else {
                    messages.forEach(msg => appendMessage(msg));
                    scrollToBottom();
                }
                fetchConversations(); // تحديث قائمة المحادثات بعد فتح المحادثة لتحديث عدادات غير المقروءة

            } catch (error) {
                console.error('Error loading messages:', error);
                messagesContainer.innerHTML = '<div class="text-center text-danger p-3">حدث خطأ أثناء تحميل الرسائل.</div>';
            }
        }

        // دالة لإضافة رسالة إلى الشات UI
        function appendMessage(message) {
            const isSender = message.sender_id === currentUserId;
            const messageClass = isSender ? 'chat-message-right' : 'chat-message-left';
            const avatar = message.sender.profile_picture_url || 'https://bootdey.com/img/Content/avatar/avatar7.png';
            const senderName = isSender ? 'أنت' : message.sender.full_name;
            const time = new Date(message.created_at).toLocaleTimeString('ar-EG', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const messageHtml = `
                <div class="${messageClass}">
                    <div>
                        <img src="${avatar}" class="rounded-circle mr-1" alt="${senderName}" width="40" height="40">
                        <div class="text-muted small text-nowrap mt-2">${time}</div>
                    </div>
                    <div class="flex-shrink-1 bg-light rounded py-2 px-3 ${isSender ? 'mr-3' : 'ml-3'} text-right">
                        <div class="font-weight-bold mb-1 ${isSender ? 'text-left' : ''}">${senderName}</div>
                        ${message.content}
                        ${message.attachment_url ? `<a href="${message.attachment_url}" target="_blank" class="d-block mt-2">عرض المرفق</a>` : ''}
                    </div>
                </div>
            `;
            messagesContainer.innerHTML += messageHtml;
            scrollToBottom();
        }

        // دالة للتمرير لأسفل الشات
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        // إرسال الرسالة عند الضغط على الزر أو Enter
        sendMessageBtn.addEventListener('click', sendMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // منع الإدخال سطر جديد
                sendMessage();
            }
        });

        async function sendMessage() {
            const content = messageInput.value.trim();
            if (!content || !currentConversationId) {
                return;
            }

            try {
                const response = await axios.post(`/api/conversations/${currentConversationId}/messages`, {
                    content: content
                });
                const newMessage = response.data;
                appendMessage(newMessage); // أضف الرسالة فوراً للـ UI الخاص بالمرسل
                messageInput.value = ''; // مسح حقل الإدخال
                fetchConversations(); // تحديث قائمة المحادثات لإظهار آخر رسالة وتحديث عداد غير المقروءة


            } catch (error) {
                console.error('Error sending message:', error);
                alert('حدث خطأ أثناء إرسال الرسالة.');
            }
        }

        // دالة لبدء محادثة جديدة من زر "راسِل المالك" في صفحة المعدة مثلاً
        // هذه الدالة ستكون متاحة بشكل عام (مثلاً في ملف JS آخر أو في الـ master)
        window.startChatWithOwner = async function(otherUserId, equipmentId = null) {
            try {
                const response = await axios.post('/api/conversations', {
                    other_user_id: otherUserId,
                    equipment_id: equipmentId
                });
                const conversation = response.data;

                // توجيه المستخدم لصفحة الشات مع تمرير الـ ID
                window.location.href = `/chat?conversation_id=${conversation.id}`;

            } catch (error) {
                console.error('Error starting chat:', error);
                alert('حدث خطأ أثناء بدء المحادثة.');
            }
        }


        // عند تحميل الصفحة
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const conversationIdParam = urlParams.get('conversation_id');

            if (conversationIdParam) {
                // جلب قائمة المحادثات أولاً
                fetchConversations().then(() => {
                    // بعد جلب قائمة المحادثات، ابحث عن المحادثة المطلوبة وافتحها
                    // نستخدم querySelectorAll ونمر على النتائج لأن openChat قد تغير الـ ID أحياناً
                    const conversationLinks = document.querySelectorAll('#conversations-list .list-group-item');
                    conversationLinks.forEach(link => {
                        // طريقة بديلة للعثور على الرابط بناءً على الـ conversationId
                        // ستحتاج إلى طريقة للوصول إلى conv.id من الرابط (مثلاً data attribute)
                        // أو ببساطة، بعد جلب المحادثات، نقوم بفتح المحادثة بالـ ID مباشرةً

                        // بما أن openChat تتطلب otherUserName, otherUserAvatar
                        // ستحتاج إلى جلب تفاصيل المحادثة أولاً
                        axios.get(`/api/conversations/${conversationIdParam}`) // يجب أن تضيف هذا الـ API في الـ controller
                             .then(response => {
                                 const convDetails = response.data;
                                 const otherUser = convDetails.other_user;
                                 const otherUserName = otherUser.full_name || `${otherUser.first_name} ${otherUser.last_name}`;
                                 const otherUserAvatar = otherUser.profile_picture_url || 'https://bootdey.com/img/Content/avatar/avatar7.png';
                                 openChat(convDetails.id, otherUserName, otherUserAvatar);
                             })
                             .catch(err => {
                                 console.error('Error fetching specific conversation for direct link:', err);
                                 alert('تعذر فتح المحادثة المطلوبة.');
                                 fetchConversations(); // ارجع لعرض جميع المحادثات
                             });
                    });
                });
            } else {
                fetchConversations();
            }
        });

    </script>
@endpush