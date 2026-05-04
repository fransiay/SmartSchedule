<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $message;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $message, $type = 'info')
    {
        $this->task = $task;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // On sauvegarde systématiquement en base de données pour le Web
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     * C'est ce qui sera stocké dans la colonne 'data' de la table notifications.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title'   => $this->task->title,
            'message' => $this->message,
            'type'    => $this->type, // info, success, warning, error
            'action'  => '/tasks',
        ];
    }
}
