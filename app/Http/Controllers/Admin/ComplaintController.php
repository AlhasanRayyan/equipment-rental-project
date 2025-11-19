<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User; // سنستخدم نموذج الرسائل لتمثيل الشكاوى
use Illuminate\Http\Request;

// لربط الشكاوى بالمستخدمين

class ComplaintController extends Controller
{
    /**
     * عرض قائمة بالشكاوى والاستفسارات.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query        = $request->input('query');
        $statusFilter = $request->input('status', 'unread'); // افتراضياً، اعرض الشكاوى غير المقروءة
        $typeFilter   = $request->input('type', 'all');      // فلتر حسب نوع الرسالة (إذا أضفت أنواعاً مخصصة)

        $complaints = Message::query()
            ->whereIn('message_type', ['complaint', 'inquiry', 'notification']) // أنواع رسائل يمكن اعتبارها شكاوى/استفسارات
            ->when($query, function ($q, $query) {
                $q->where('content', 'like', "%{$query}%")
                    ->orWhereHas('sender', function ($sq) use ($query) {
                        $sq->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    });
            })
            ->when($statusFilter === 'unread', function ($q) {
                $q->where('is_read', false);
            })
            ->when($statusFilter === 'read', function ($q) {
                $q->where('is_read', true);
            })
            ->when($statusFilter === 'resolved', function ($q) {
                $q->where('is_resolved', true); // نفترض وجود حقل 'is_resolved'
            })
            ->when($typeFilter !== 'all', function ($q) use ($typeFilter) {
                $q->where('message_type', $typeFilter);
            })
            ->with('sender') // تحميل علاقة المرسل
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // جلب أنواع الرسائل الفريدة للفلترة (إذا كانت محددة في قاعدة البيانات)
        $messageTypes = Message::select('message_type')->distinct()->pluck('message_type');
        // هنا يمكنك تصفية $messageTypes لعرض فقط الأنواع التي تعتبر شكاوى/استفسارات

        return view('dashboard.complaints.index', compact('complaints', 'query', 'statusFilter', 'typeFilter', 'messageTypes'));
    }

    /**
     * عرض تفاصيل شكوى أو استفسار محدد.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\View\View
     */
    public function show(Message $message)
    {
        // تأكد أنها شكوى أو استفسار
        if (! in_array($message->message_type, ['complaint', 'inquiry', 'notification'])) {
            abort(404, 'الرسالة ليست شكوى أو استفسار.');
        }

        $message->markAsRead(); // قم بتمييزها كمقروءة عند العرض

        return view('dashboard.complaints.show', compact('message'));
    }

    /**
     * تمييز شكوى أو استفسار كمقروء.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Message $message)
    {
        $message->markAsRead(); // استخدام الدالة من الـ Model

        return redirect()->route('admin.complaints.index')->with('success', 'تم تمييز الشكوى كمقروءة.');
    }

    /**
     * حل شكوى أو استفسار.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resolve(Message $message)
    {
        // نفترض أن لديك حقل 'is_resolved' في جدول الرسائل أو لديك آلية أخرى للحل.
        // يجب أن تقوم بتعديل migration الخاص بـ Messages لإضافة هذا الحقل.
        $message->update(['is_resolved' => true]);
        // يمكنك هنا أيضاً إرسال رد للمستخدم أو تسجيل الإجراء

        return redirect()->route('admin.complaints.index')->with('success', 'تم حل الشكوى بنجاح.');
    }

    /**
     * حذف شكوى أو استفسار.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.complaints.index')->with('success', 'تم حذف الشكوى بنجاح.');
    }

    public function trash(Request $request)
    {
        $query = $request->input('query');

        $complaints = Message::onlyTrashed()
            ->whereIn('message_type', ['complaint', 'inquiry'])
            ->when($query, function ($q, $query) {
                $q->where('content', 'like', "%{$query}%")
                    ->orWhereHas('sender', function ($sq) use ($query) {
                        $sq->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    });
            })
            ->with('sender')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('dashboard.complaints.trash', compact('complaints', 'query'));
    }

    public function restore($id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();

        return redirect()->route('admin.complaints.trash')->with('success', 'تم استعادة الشكوى بنجاح.');
    }

    public function restoreAll()
    {
        Message::onlyTrashed()
            ->whereIn('message_type', ['complaint', 'inquiry'])
            ->restore();

        return redirect()->route('admin.complaints.trash')->with('success', 'تم استعادة جميع الشكاوى المحذوفة.');
    }

    public function forceDelete($id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->forceDelete();

        return redirect()->route('admin.complaints.trash')->with('success', 'تم حذف الشكوى نهائياً.');
    }

    public function forceDeleteAll()
    {
        Message::onlyTrashed()
            ->whereIn('message_type', ['complaint', 'inquiry'])
            ->forceDelete();

        return back()->with('success', 'تم حذف جميع الشكاوى نهائياً.');
    }

    public function archived(Request $request)
    {
        $query      = $request->input('query');
        $typeFilter = $request->input('type', 'all');

        $complaints = Message::query()
            ->whereIn('message_type', ['complaint', 'inquiry', 'notification'])
            ->where('is_resolved', true) //  هنا الفرق: نجيب بس المحلولة/المؤرشفة
            ->when($query, function ($q, $query) {
                $q->where('content', 'like', "%{$query}%")
                    ->orWhereHas('sender', function ($sq) use ($query) {
                        $sq->where('first_name', 'like', "%{$query}%")
                            ->orWhere('last_name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%");
                    });
            })
            ->when($typeFilter !== 'all', function ($q) use ($typeFilter) {
                $q->where('message_type', $typeFilter);
            })
            ->with('sender')
            ->orderBy('updated_at', 'desc') // الأحدث حلاً أولاً
            ->paginate(10);

        $messageTypes = Message::select('message_type')->distinct()->pluck('message_type');

        // نمرر فلتر ثابت عشان الواجهة تعرف إنها صفحة أرشيف
        $statusFilter   = 'resolved';
        $isArchivedPage = true;

        return view('dashboard.complaints.index', compact(
            'complaints',
            'query',
            'statusFilter',
            'typeFilter',
            'messageTypes',
            'isArchivedPage'
        ));
    }

}
