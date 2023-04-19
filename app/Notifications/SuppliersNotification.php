<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Supplier;
use App\Models\SupplierContact;
use Illuminate\Notifications\Notification;

class SuppliersNotification extends Notification
{
    use Queueable;
    private $supplier;
    private $contacts;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($supplier, $contacts)
    {
        $this->supplier = $supplier;
        $this->contacts = $contacts;
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
    public function toArray($notifiable)
    {
        return [
            "Supplier Name Added Successfully: {$this->supplier->name}",
            "Contacts added Successfully: ". implode(", ", collect($this->contacts)->pluck('supplier_name')->toArray())
        ];
    }
}
