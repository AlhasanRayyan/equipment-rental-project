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
