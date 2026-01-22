<?php

namespace Projects\WellmedBackbone\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrescriptionCreated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['firebase'];
        // return ['mail'];
    }

    /**
     * Get the firebase representation of the notification.
     */
    public function toFirebase(object $notifiable)
    {
        $messaging = app('firebase.messaging');
        $message = CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(FcmNotification::create(
                'Patient Visit Created',
                'Visit ID: '.$this->visit->id
            ))
            ->withData([
                'visit_id' => (string) $this->visit->id,
                'patient_name' => $this->visit->patient->name,
            ]);

        return $messaging->send($message);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
