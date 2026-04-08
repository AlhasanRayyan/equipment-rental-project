@extends('layouts.app')

@section('title', 'Notification | ' . config('app.name'))

@section('styles')
    <style>
        button {
            background-color: transparent;
            border: none;
        }

        .notif-item {
            cursor: pointer;
        }

        .notif-item:hover {
            background-color: #f8f9fc;
        }

        .notif-modal {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 0.5rem 1.5rem rgba(58, 59, 69, 0.2);
            overflow: hidden;
        }

        .notif-modal__icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notif-modal__icon-wrap i {
            font-size: 1.1rem;
        }

        .notif-modal__message {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 1rem;
            line-height: 1.9;
            color: #3a3b45;
            font-weight: 500;
        }

        .notif-modal__meta {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            padding: 1rem;
        }

        .notif-modal__meta ul {
            margin: 0;
            padding-right: 1rem;
        }

        .notif-modal__meta li {
            margin-bottom: .35rem;
        }

        .notif-modal__kind {
            background: #f3f6ff;
            color: #4e73df;
            border-radius: 999px;
            padding: .35rem .75rem;
            font-size: .8rem;
        }

        .list-group-item {
            border-right: 0;
            border-left: 0;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                الإشعارات غير المقروءة ({{ auth()->user()->unreadNotifications->count() }})
            </h5>

            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.notifications.readall') }}">
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

                                        <form action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                            method="POST" class="m-0">
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


            {{-- مودال الادمن للاشعارات --}}
            <div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-end notif-modal" dir="rtl">

                        <div class="modal-header border-0 pb-2">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <div class="notif-modal__icon-wrap">
                                    <i id="notifModalIcon" class="fas fa-bell text-dark"></i>
                                </div>

                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h5 class="modal-title mb-0 fw-bold" id="notifModalTitle">إشعار</h5>
                                        <span id="notifModalLabel"
                                            class="badge rounded-pill text-bg-light border px-3 py-2"></span>
                                    </div>
                                    <small class="text-muted d-block mt-1" id="notifModalTime"></small>
                                </div>

                                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                        </div>

                        <div class="modal-body pt-2">
                            <div class="notif-modal__message" id="notifModalMessage"></div>

                            <div class="notif-modal__meta mt-3" id="notifExtraWrap" style="display:none;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="fas fa-circle-info text-secondary"></i>
                                    <strong class="small">تفاصيل إضافية</strong>
                                </div>
                                <div class="small text-muted" id="notifModalMeta"></div>
                            </div>
{{--
                            <div class="mt-3">
                                <span class="small text-muted me-2">النوع:</span>
                                <code id="notifModalKind" class="notif-modal__kind">system_alert</code>
                            </div> --}}
                        </div>

                        <div
                            class="modal-footer border-0 pt-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex gap-2">
                                <form id="bookingConfirmForm" method="POST" class="m-0 d-none">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="موافقة">
                                        <i class="fas fa-check"></i>
                                        موافقة
                                    </button>
                                </form>

                                <form id="bookingCancelForm" method="POST" class="m-0 d-none">
                                    @csrf
                                    <input type="hidden" name="reason" value="تم الرفض من الإشعارات">
                                    <button type="submit" class="btn btn-danger" title="رفض">
                                        <i class="fas fa-times"></i>
                                        رفض
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
