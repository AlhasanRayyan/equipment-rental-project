@extends('layouts.app')

@section('title', 'Notification | ' . config('app.name'))
@section('styles')
    <style>
        button {
            background-color: transparent;
            border: none;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                الإشعارات غير المقروءة ({{ auth()->user()->unreadNotifications->count() }})
            </h5>

            <a class="btn btn-sm btn-outline-primary" href="{{ route('readall') }}">
                <i class="fas fa-check-double"></i>

                تعليم الكل كمقروء
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="list-group list-group-flush">
                {{-- لكل اشعار ايقونة ولو ن مختلفين  --}}

                @forelse(auth()->user()->notifications as $notification)
                    @php
                        $kind = $notification->data['kind'] ?? 'system_alert';
                        $ui = $notifUI[$kind] ?? ['icon' => 'fas fa-bell', 'class' => 'text-dark'];
                        $meta = [
                            'booking_id' => $notification->data['booking_id'] ?? null,
                            'equipment_id' => $notification->data['equipment_id'] ?? null,
                            'conversation_id' => $notification->data['conversation_id'] ?? null,
                            'message_id' => $notification->data['message_id'] ?? null,
                            'lat' => $notification->data['lat'] ?? null,
                            'lng' => $notification->data['lng'] ?? null,
                            'speed' => $notification->data['speed'] ?? null,
                            'distance_km' => $notification->data['distance_km'] ?? null,
                            'battery_level' => $notification->data['battery_level'] ?? null,
                        ];
                    @endphp

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <a class="text-decoration-none flex-grow-1 d-flex align-items-start gap-2 notif-item"
                            data-bs-toggle="modal" data-bs-target="#notifModal" data-id="{{ $notification->id }}"
                            data-kind="{{ $kind }}" data-icon="{{ $ui['icon'] }}" data-color="{{ $ui['class'] }}"
                            data-label="{{ $ui['label'] }}" data-title="{{ $notification->data['title'] ?? $ui['label'] }}"
                            data-message="{{ $notification->data['data'] ?? '' }}"
                            data-time="{{ optional($notification->created_at)->diffForHumans() }}"
                            data-meta='@json($meta)'> {{-- href="{{ route('read', $notification->id) }}" --}}

                            <i class="{{ $ui['icon'] }} {{ $ui['class'] }} mt-1"></i>

                            <div>
                                <div class="{{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                    {{ $titles[$kind] ?? ($notification->data['data'] ?? '') }}
                                </div>
                                <small class="text-muted d-block">
                                    {{ optional($notification->created_at)->diffForHumans() }}
                                </small>
                            </div>
                        </a>

                        <button type="button" class="btn btn-sm btn-outline-danger ms-3" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $notification->id }}"> <i class="fas fa-trash"></i>


                        </button>
                        <div class="modal fade" id="deleteModal{{ $notification->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-end" dir="rtl">
                                    <div class="modal-header">
                                        <h5 class="modal-title">تأكيد الحذف</h5>
                                        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button> --}}
                                    </div>

                                    <div class="modal-body">
                                        هل أنت متأكد أنك تريد حذف هذا الإشعار؟
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">إلغاء</button>

                                        <form action="{{ route('delete', $notification->id) }}" method="POST"
                                            class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger"> <i class="fas fa-trash"></i>
                                                نعم، احذف</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-muted">لا يوجد إشعارات.</div>
                @endforelse
            </div>

            <div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-end" dir="rtl">

                        <div class="modal-header">
                            <div class="d-flex align-items-center gap-2">
                                <i id="notifModalIcon" class="fas fa-bell text-dark"></i>
                                <div>
                                    <h5 class="modal-title mb-0" id="notifModalTitle">إشعار</h5>
                                    <small class="text-muted" id="notifModalLabel"></small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-body">
                            <div class="p-3 rounded border bg-light">
                                <div class="mb-2" id="notifModalMessage"></div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span><i class="far fa-clock"></i> <span id="notifModalTime"></span></span>
                                    <span><i class="fas fa-tag"></i> <span id="notifModalKind"></span></span>
                                </div>
                            </div>

                            <div class="mt-3" id="notifExtraWrap" style="display:none;">
                                <h6 class="mb-2">تفاصيل إضافية</h6>
                                <div class="small text-muted" id="notifModalMeta"></div>
                            </div>
                        </div>

                        <div class="modal-footer d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                {{-- تظهر فقط لطلب الحجز --}}
                                {{-- <form id="bookingConfirmForm" method="POST" class="m-0 d-none">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success" title="موافقة">
                                        <i class="fas fa-check"></i> موافقة

                                    </button>
                                </form>

                                <form id="bookingCancelForm" method="POST" class="m-0 d-none">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="reason" value="تم الرفض من الإشعارات">
                                    <button type="submit" class="btn btn-danger" title="رفض">
                                        <i class="fas fa-times"></i> رفض

                                    </button>
                                </form> --}}
                                <form id="bookingConfirmForm" method="POST" class="m-0 d-none">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="موافقة">
                                        <i class="fas fa-check"></i> موافقة
                                    </button>
                                </form>

                                <form id="bookingCancelForm" method="POST" class="m-0 d-none">
                                    @csrf
                                    <input type="hidden" name="reason" value="تم الرفض من الإشعارات">
                                    <button type="submit" class="btn btn-danger" title="رفض">
                                        <i class="fas fa-times"></i> رفض
                                    </button>
                                </form>
                            </div>

                            <div class="d-flex gap-2">
                                <form id="markAsReadForm" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-primary" title="تعليم كمقروء">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                </form>

                                <form id="deleteNotifForm" method="POST" class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="حذف الإشعار">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="إغلاق">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
