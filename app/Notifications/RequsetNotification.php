<?php

namespace App\Notifications;
use App\Models\Need;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequsetNotification extends Notification
{
    use Queueable;

    private $send;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($send)
    {
        $this->send = $send;
\Log::debug($this->send->model);


    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable){

        return [    
            "Requested :Type:-{$this->send->type->name},Model:-{$this->send->model},Request Reason:{$this->send->requestreason}-,Requested_by :{$this->send->user->firstname}",
        ];
    }
}