<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
//use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SlackMessage;


class SlackErrorNotification extends Notification
{
    use Queueable;
    protected $exception;
    /**
     * Create a new notification instance.
     */
    public function __construct($exception)
    {
        //
        $this->exception = $exception;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');

        return (new SlackMessage)
            ->error()
            ->content("🚨 Exception Occurred")
            ->attachment(function ($attachment) {
                $attachment->title($this->exception->getMessage())
                           ->fields([
                               'File' => $this->exception->getFile(),
                               'Line' => $this->exception->getLine(),
                               'Trace' => substr($this->exception->getTraceAsString(), 0, 500)
                           ]);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
