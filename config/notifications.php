<?php

return [

    'ui' => [
        'booking_request' => [
            'icon' => 'fas fa-calendar-plus',
            'class' => 'text-primary',
            'label' => 'حجز',
        ],
        'booking_confirmed' => [
            'icon' => 'fas fa-check-circle',
            'class' => 'text-success',
            'label' => 'حجز',
        ],
        'booking_cancelled' => [
            'icon' => 'fas fa-times-circle',
            'class' => 'text-danger',
            'label' => 'حجز',
        ],

        'payment_received' => [
            'icon' => 'fas fa-money-bill-wave',
            'class' => 'text-success',
            'label' => 'دفع',
        ],
        'payment_failed' => [
            'icon' => 'fas fa-exclamation-triangle',
            'class' => 'text-warning',
            'label' => 'دفع',
        ],
        'refund_issued' => [
            'icon' => 'fas fa-undo',
            'class' => 'text-secondary',
            'label' => 'دفع',
        ],
        'equipment_moved' => [
            'icon' => 'fas fa-location-arrow',
            'class' => 'text-danger',
            'label' => 'GPS',
        ],
        'equipment_created' => [
            'icon' => 'fas fa-hard-hat',
            'class' => 'text-primary',
            'label' => 'معدات',
        ],
        'system_alert' => [
            'icon' => 'fas fa-bell',
            'class' => 'text-dark',
            'label' => 'تنبيه',
        ],
    ],

    'titles' => [
        'booking_request' => 'طلب حجز جديد',
        'booking_confirmed' => 'تم تأكيد الحجز',
        'booking_cancelled' => 'تم إلغاء الحجز',
        'payment_received' => 'تم استلام الدفع',
        'payment_failed' => 'فشل الدفع',
        'refund_issued' => 'تم إصدار استرداد',
        'equipment_moved' => 'تحذير حركة',
        'equipment_created' => 'معدة جديدة',
        'system_alert' => 'إشعار',
    ],

];
