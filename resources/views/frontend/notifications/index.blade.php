@extends('layouts.master')
@section('title', 'الإشعارات')

@section('content')
<div class="container" style="padding: 40px 0;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="m-0">الإشعارات</h4>

        <form action="{{ route('front.notifications.readall') }}" method="POST" class="m-0">
            @csrf
            @method('PUT')
            <button class="btn btn-sm btn-outline-dark" type="submit" title="تعليم الكل كمقروء">
                <i class="fas fa-check-double"></i>
                تعليم الكل كمقروء
            </button>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">

            @forelse($notifications as $n)
                @php
                    $kind = $n->data['kind'] ?? 'system_alert';

                    // نفس mapping اللي عندك (إذا بتحبي خليها بـ include)
                    $notifUI = [
                        'booking_request'   => ['icon'=>'fas fa-calendar-plus', 'class'=>'text-primary', 'label'=>'حجز'],
                        'booking_confirmed' => ['icon'=>'fas fa-check-circle',  'class'=>'text-success', 'label'=>'حجز'],
                        'booking_cancelled' => ['icon'=>'fas fa-times-circle',  'class'=>'text-danger',  'label'=>'حجز'],
                        'new_message'       => ['icon'=>'fas fa-envelope',      'class'=>'text-info',    'label'=>'رسائل'],
                        'payment_received'  => ['icon'=>'fas fa-money-bill-wave','class'=>'text-success','label'=>'دفع'],
                        'payment_failed'    => ['icon'=>'fas fa-exclamation-triangle','class'=>'text-warning','label'=>'دفع'],
                        'refund_issued'     => ['icon'=>'fas fa-undo',          'class'=>'text-secondary','label'=>'دفع'],
                        'equipment_moved'   => ['icon'=>'fas fa-location-arrow','class'=>'text-danger',  'label'=>'GPS'],
                        'system_alert'      => ['icon'=>'fas fa-bell',          'class'=>'text-dark',    'label'=>'تنبيه'],
                    ];

                    $ui = $notifUI[$kind] ?? ['icon'=>'fas fa-bell','class'=>'text-dark','label'=>'تنبيه'];

                    $title = $n->data['title'] ?? 'إشعار';
                    $msg   = $n->data['data'] ?? '';
                @endphp

                <a href="#"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-2 notif-item-btn"
                   style="{{ $n->read_at ? 'opacity:.7;' : 'font-weight:700;' }}"
                   data-bs-toggle="modal"
                   data-bs-target="#userNotifModal"
                   data-id="{{ $n->id }}"
                   data-kind="{{ $kind }}"
                   data-icon="{{ $ui['icon'] }}"
                   data-color="{{ $ui['class'] }}"
                   data-label="{{ $ui['label'] }}"
                   data-title="{{ $title }}"
                   data-message="{{ $msg }}"
                   data-time="{{ optional($n->created_at)->diffForHumans() }}"
                   data-meta='@json($n->data)'>
                    <div class="d-flex gap-2">
                        <i class="{{ $ui['icon'] }} {{ $ui['class'] }}" style="margin-top:3px;"></i>
                        <div>
                            <div>{{ $title }}</div>
                            <small class="text-muted">{{ optional($n->created_at)->diffForHumans() }}</small>
                        </div>
                    </div>

                    <span class="badge bg-light text-dark border">{{ $ui['label'] }}</span>
                </a>

            @empty
                <div class="list-group-item text-muted">لا يوجد إشعارات.</div>
            @endforelse

        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
</div>

{{-- المودال --}}
<div class="modal fade" id="userNotifModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-end" dir="rtl">

            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                        style="width:44px;height:44px;background:#f1f3f5;">
                        <i id="userNotifModalIcon" class="fas fa-bell text-dark" style="font-size:18px;"></i>
                    </div>

                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title mb-0" id="userNotifModalTitle">إشعار</h5>
                            {{-- <span id="userNotifModalLabel" class="badge bg-light text-dark border"></span> --}}
                        </div>
                        <small class="text-muted d-block" id="userNotifModalTime"></small>
                    </div>
                </div>

                {{-- <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>

            <div class="modal-body">
                <div id="userNotifModalMessage" class="mb-3" style="line-height:1.9;"></div>

                <div id="userNotifExtraWrap" class="border rounded p-3 bg-light" style="display:none;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-info-circle text-secondary"></i>
                        <strong class="small">تفاصيل</strong>
                    </div>
                    <div id="userNotifModalMeta" class="small text-muted"></div>
                </div>

                <div class="mt-3 small text-muted">
                    <span class="me-2">نوع:</span>
                    <code id="userNotifModalKind">system_alert</code>
                </div>
            </div>

            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <form id="userMarkAsReadForm" method="POST" class="m-0">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-outline-primary" title="تعليم كمقروء">
                            <i class="fas fa-check-double"></i>
                        </button>
                    </form>

                    <form id="userDeleteNotifForm" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="إغلاق">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
