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
    @php
        $notifUI = config('notifications.ui');
        $titles = config('notifications.titles');
    @endphp
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                الإشعارات غير المقروءة ({{ auth()->user()->unreadNotifications->count() }})
            </h5>

            <form action="{{ route('admin.notifications.readall') }}" method="POST" class="m-0">
                @csrf
                @method('PUT')
                <button class="btn btn-sm btn-outline-primary" type="submit" title="تعليم الكل كمقروء">
                    <i class="fas fa-check-double"></i>
                    تعليم الكل كمقروء
                </button>
            </form>
        </div>

        <div class="card shadow-sm">
            <div class="list-group list-group-flush">
                {{-- لكل اشعار ايقونة ولو ن مختلفين  --}}

                @forelse($notifications as $notification)
                    @php
                        $kind = $notification->data['kind'] ?? 'system_alert';
                        $ui = $notifUI[$kind] ?? [
                            'icon' => 'fas fa-bell',
                            'class' => 'text-dark',
                            'label' => 'تنبيه',
                        ];

                        $meta = [
                            'booking_id' => $notification->data['booking_id'] ?? null,
                            'equipment_name' => $notification->data['equipment_name'] ?? null,
                            'renter_name' => $notification->data['renter_name'] ?? null,
                            'owner_name' => $notification->data['owner_name'] ?? null,
                            'amount' => $notification->data['amount'] ?? null,
                            'reason' => $notification->data['reason'] ?? null,
                            'start_date' => $notification->data['start_date'] ?? null,
                            'end_date' => $notification->data['end_date'] ?? null,
                            'distance_km' => $notification->data['distance_km'] ?? null,
                            'location_text' => $notification->data['location_text'] ?? null,
                            'login_at' => $notification->data['login_at'] ?? null,
                            'registered_at' => $notification->data['registered_at'] ?? null,
                        ];
                    @endphp

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <a class="text-decoration-none flex-grow-1 d-flex align-items-start gap-2 notif-item"
                            data-bs-toggle="modal" data-bs-target="#notifModal" data-id="{{ $notification->id }}"
                            data-kind="{{ $kind }}" data-icon="{{ $ui['icon'] }}" data-color="{{ $ui['class'] }}"
                            data-label="{{ $ui['label'] }}" data-title="{{ $notification->data['title'] ?? $ui['label'] }}"
                            data-message="{{ $notification->data['data'] ?? '' }}"
                            data-time="{{ optional($notification->created_at)->diffForHumans() }}"
                            data-url="{{ $notification->data['url'] ?? '' }}" data-meta='@json($meta)'>

                            <i class="{{ $ui['icon'] }} {{ $ui['class'] }} mt-1"></i>

                            <div>
                                <div class="{{ $notification->read_at ? 'text-muted' : 'fw-bold' }}">
                                    {{ $notification->data['title'] ?? ($titles[$kind] ?? 'إشعار') }}
                                </div>
                                <small class="text-muted d-block">
                                    {{ optional($notification->created_at)->diffForHumans() }}
                                </small>
                            </div>
                        </a>

                        <button type="button" class="btn btn-sm btn-outline-danger ms-3" data-bs-toggle="modal"
                            data-bs-target="#deleteModal{{ $notification->id }}">
                            <i class="fas fa-trash"></i>
                        </button>

                        <div class="modal fade" id="deleteModal{{ $notification->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-end" dir="rtl">
                                    <div class="modal-header">
                                        <h5 class="modal-title">تأكيد الحذف</h5>
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
                                            <button class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                                نعم، احذف
                                            </button>
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

            @if ($notifications->hasPages())
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $notifications->links() }}
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection
