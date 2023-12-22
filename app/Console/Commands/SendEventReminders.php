<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Notifications\EventReminderNotification;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send event reminders to all event attendees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $events = Event::with('attendees.user')
            ->whereBetween('start_time', [now(), now()->addDay()])
            ->get();

        $eventCount = $events->count();
        $eventLabel = Str::plural('event', $eventCount);

        $this->info("Sending reminders for {$eventCount} {$eventLabel}...");

        $events->each(
            fn ($event) => $event->attendees->each(
                // fn ($attendee) => $this->info("Notifying the user {$attendee->user->name} about the event {$event->name}}")
                fn ($attendee) => $attendee->user->notify(
                    new EventReminderNotification($event)
                )
            )
        );

        $this->info('Reminder notifcation sent successfully!');
    }
}
