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
        @php
            $notifUI = config('notifications.ui');
            $titles = config('notifications.titles');
        @endphp
        <div class="card shadow-sm">
            <div class="list-group list-group-flush">

                @forelse($notifications as $n)
                    @php
                        $kind = $n->data['kind'] ?? 'system_alert';

                        $ui = $notifUI[$kind] ?? [
                            'icon' => 'fas fa-bell',
                            'class' => 'text-dark',
                            'label' => 'تنبيه',
                        ];

                        $title = $n->data['title'] ?? ($titles[$kind] ?? 'إشعار');
                        $msg = $n->data['data'] ?? '';
                    @endphp

                    <a href="#"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-2 notif-item-btn"
                        style="{{ $n->read_at ? 'opacity:.7;' : 'font-weight:700;' }}" data-bs-toggle="modal"
                        data-bs-target="#userNotifModal" data-id="{{ $n->id }}" data-kind="{{ $kind }}"
                        data-icon="{{ $ui['icon'] }}" data-color="{{ $ui['class'] }}" data-label="{{ $ui['label'] }}"
                        data-title="{{ $title }}" data-message="{{ $msg }}"
                        data-time="{{ optional($n->created_at)->diffForHumans() }}" data-meta='@json($n->data)'
                        data-url="{{ $n->data['url'] ?? '' }}">
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


@endsection
