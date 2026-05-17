<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class TaskNotification extends Notification
{
    use Queueable;

    protected $task;
    protected $message;
    protected $type;
    protected $reminderType;

    /**
     * Create a new notification instance.
     */
    public function __construct($task, $message, $type = 'info', $reminderType = null)
    {
        $this->task = $task;
        $this->message = $message;
        $this->type = $type;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Envoi de la notification Push via Expo si le token est présent
        if (!empty($notifiable->expo_push_token)) {
            $this->sendPushNotification($notifiable->expo_push_token);
        }

        // Sauvegarde systématique en base de données pour l'historique
        return ['database'];
    }

    /**
     * Envoie la notification push à l'API Expo.
     */
    protected function sendPushNotification($token)
    {
        try {
            Http::post('https://exp.host/--/api/v2/push/send', [
                'to'    => $token,
                'title' => 'Rappel de tâche',
                'body'  => $this->message,
                'data'  => [
                    'task_id' => $this->task->id,
                    'type'    => $this->type
                ],
                'sound' => 'default',
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to send Expo push notification: " . $e->getMessage());
        }
    }

    /**
     * Get the array representation of the notification.
     * C'est ce qui sera stocké dans la colonne 'data' de la table notifications.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $data = [
            'task_id' => $this->task->id,
            'title'   => $this->task->title,
            'message' => $this->message,
            'type'    => $this->type, // info, success, warning, error
            'action'  => '/tasks',
        ];

        if ($this->reminderType) {
            $data['reminder_type'] = $this->reminderType;
        }

        return $data;
    }
}
