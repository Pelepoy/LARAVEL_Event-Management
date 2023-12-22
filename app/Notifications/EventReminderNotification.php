<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Event $event,
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line("Greetings! {$this->event->user->name}. This is a reminder for the event  {$this->event->name}")
            ->action('View Event', route('events.show', $this->event->id))
            ->line(
                "The event {$this->event->name} will start at {$this->event->start_time} and end at {$this->event->end_time}"
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id'         =>  $this->event->id,
            'event_name'       =>  $this->event->name,
            'event_start_time' =>  $this->event->start_time,
            'event_end_time'   =>  $this->event->end_time,
        ];
    }
}
