<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $kind,
        public string $title,
        public string $message,
        public ?string $url = null,
        public array $meta = [],
        public bool $sendMail = false
    ) {}

    public function via($notifiable): array
    {
        return $this->sendMail ? ['database', 'mail'] : ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'kind'  => $this->kind,
            'title' => $this->title,
            'data'  => $this->message,
            'url'   => $this->url,
            ...$this->meta, // booking_id, equipment_id, amount...
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $m = (new MailMessage)
            ->subject($this->title)
            ->line($this->message);

        if ($this->url) $m->action('فتح التفاصيل', $this->url);

        return $m;
    }
}
// namespace App\Notifications;

// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Notifications\Messages\MailMessage;
// use Illuminate\Notifications\Notification;

// class AppAlertNotification extends Notification
// {
//     use Queueable;

//     /**
//      * Create a new notification instance.
//      */
//     public function __construct(
//         public string $kind,
//         public string $title,
//         public string $message,
//         public ?string $url = null,
//         public array $meta = []
//     ) {}

//     /**
//      * Get the notification's delivery channels.
//      *
//      * @return array<int, string>
//      */
//     public function via($notifiable): array
//     {
//         return ['database']; // لاحقاً بسهولة تضيفي 'mail'
//     }

//     public function toDatabase($notifiable): array
//     {
//         return array_merge([
//             'kind' => $this->kind,
//             'title' => $this->title,
//             'data' => $this->message,
//             'url' => $this->url,
//         ], $this->meta);
//     }

//     /**
//      * Get the mail representation of the notification.
//      */
//     // public function toMail(object $notifiable): MailMessage
//     // {
//     //     return (new MailMessage)
//     //                 ->line('The introduction to the notification.')
//     //                 ->action('Notification Action', url('/'))
//     //                 ->line('Thank you for using our application!');
//     // }

//     /**
//      * Get the array representation of the notification.
//      *
//      * @return array<string, mixed>
//      */
//     public function toArray(object $notifiable): array
//     {
//         return [
//             //
//         ];
//     }
// }
