<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Equipment; // قد تحتاجها عند بدء محادثة جديدة مرتبطة بمعدة
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChatController extends Controller
{
    // جلب قائمة المحادثات للمستخدم الحالي
    public function index(Request $request)
    {
        $user = Auth::user();

        // جلب المحادثات التي يكون المستخدم الحالي إما المالك أو المستأجر فيها
        $conversations = Conversation::where('owner_id', $user->id)
                                    ->orWhere('renter_id', $user->id)
                                    ->orderByDesc('last_message_at') // ترتيب حسب آخر رسالة
                                    ->with(['owner', 'renter', 'equipment', 'messages' => function($q) {
                                        $q->latest()->take(1); // جلب آخر رسالة لكل محادثة
                                    }])
                                    ->get();

        // حساب الرسائل غير المقروءة لكل محادثة
        $conversations->each(function ($conversation) use ($user) {
            $conversation->unread_messages_count = $conversation->messages()
                                                                ->where('is_read', false)
                                                                ->where('receiver_id', $user->id) // الرسائل التي استلمها المستخدم ولم يقرأها
                                                                ->count();
            // أضف المستخدم الآخر كـ attribute للمحادثة لتسهيل الوصول إليه في الفرونت إند
            $conversation->append('other_user');
        });

        return response()->json($conversations);
    }

    // جلب رسائل محادثة معينة
    public function showMessages(Conversation $conversation)
    {
        $user = Auth::user();

        // تحقق أن المستخدم الحالي جزء من المحادثة
        if ($user->id !== $conversation->owner_id && $user->id !== $conversation->renter_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // جلب الرسائل وترتيبها زمنياً مع معلومات المرسل
        $messages = $conversation->messages()->with('sender:id,first_name,last_name,profile_picture_url')->orderBy('created_at')->get();

        // تحديد جميع رسائل الطرف الآخر كمقروءة عند فتح المحادثة
        $conversation->messages()
                     ->where('receiver_id', $user->id)
                     ->where('is_read', false)
                     ->update(['is_read' => true]);

        return response()->json($messages);
    }

    // إرسال رسالة جديدة في محادثة موجودة
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = Auth::user();

        // تحقق أن المستخدم الحالي جزء من المحادثة
        if ($user->id !== $conversation->owner_id && $user->id !== $conversation->renter_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required_without_all:attachment|string|max:1000',
            // 'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048', // للتعامل مع المرفقات
        ]);

        // تحديد المستقبِل
        $receiver = ($user->id === $conversation->owner_id) ? $conversation->renter : $conversation->owner;

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'content' => $request->content,
            'is_read' => false,
            'message_type' => 'text', // أو 'attachment' إذا كنت ستدعم المرفقات
            // 'attachment_url' => '', // سيتم تحديثه إذا كان هناك مرفق
        ]);

        // // إذا كان فيه مرفق (أتركها معلقة حالياً إذا لم تكن تدعمها الآن)
        // if ($request->hasFile('attachment')) {
        //     $path = $request->file('attachment')->store('attachments/messages', 'public');
        //     $message->attachment_url = asset('storage/' . $path);
        //     $message->save();
        // }

        // تحديث وقت آخر رسالة في المحادثة
        $conversation->update(['last_message_at' => now()]);

        // إرسال الحدث عبر WebSockets
        broadcast(new MessageSent($message->load('sender'), $user, $receiver))->toOthers();

        return response()->json($message->load('sender'), 201);
    }

    // بدء محادثة جديدة أو جلب محادثة موجودة
    public function startOrGetConversation(Request $request)
    {
        $user = Auth::user(); // المستخدم الحالي هو "المستأجر" الذي يضغط زر "راسِل المالك"

        $request->validate([
            'other_user_id' => 'required|exists:users,id', // id الطرف الآخر (المالك)
            'equipment_id' => 'nullable|exists:equipment,id', // إذا كانت المحادثة مرتبطة بمعدة
        ]);

        $otherUserId = $request->other_user_id; // هذا هو المالك

        // لا يمكن للمستخدم بدء محادثة مع نفسه
        if ($user->id == $otherUserId) {
            return response()->json(['message' => 'Cannot start a conversation with yourself.'], 400);
        }

        // تحديد من هو الـ owner ومن هو الـ renter بشكل ثابت للمحادثة لضمان التناسق
        // دائمًا المالك يكون owner_id والمستأجر renter_id
        $ownerId = $otherUserId;
        $renterId = $user->id;

        // البحث عن محادثة موجودة بين هذين المستخدمين ولنفس المعدة (إذا وجدت)
        $conversation = Conversation::where('owner_id', $ownerId)
                                    ->where('renter_id', $renterId)
                                    ->when($request->equipment_id, function ($query, $equipmentId) {
                                        $query->where('equipment_id', $equipmentId);
                                    })
                                    ->first();

        if (!$conversation) {
            // إذا لم توجد محادثة، نقوم بإنشاء واحدة جديدة
            $conversation = Conversation::create([
                'owner_id' => $ownerId,
                'renter_id' => $renterId,
                'equipment_id' => $request->equipment_id,
                'last_message_at' => now(),
            ]);
        }

        // نرجع المحادثة مع معلومات الطرفين والمعدة لتسهيل عرضها في الفرونت إند
        return response()->json($conversation->load(['owner', 'renter', 'equipment']), 200);
    }

    // هذه الدالة الآن غير ضرورية لأننا نحدد الرسائل كمقروءة تلقائياً في showMessages
    // ولكن يمكن الاحتفاظ بها لسيناريوهات أخرى
    // public function markAsRead(Message $message)
    // {
    //     $user = Auth::user();
    //     if ($user->id !== $message->receiver_id) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }
    //     $message->markAsRead();
    //     return response()->json(['message' => 'Message marked as read']);
    // }


    
    public function getConversationDetails(Conversation $conversation)
    {
        $user = Auth::user();

        // تحقق أن المستخدم الحالي جزء من المحادثة
        if ($user->id !== $conversation->owner_id && $user->id !== $conversation->renter_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // تحميل العلاقات الضرورية وإضافة الـ accessor
        $conversation->load(['owner', 'renter', 'equipment']);
        $conversation->append('other_user'); // تأكد أن هذا الـ accessor موجود في موديل Conversation

        return response()->json($conversation);
    }
}