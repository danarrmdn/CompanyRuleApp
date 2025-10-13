<?php

namespace App\Notifications;

use App\Models\CompanyRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDocumentNotification extends Notification
{
    use Queueable;

    protected $rule;

    /**
     * Create a new notification instance.
     */
    public function __construct(CompanyRule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'rule_id' => $this->rule->id,
            'message' => 'Dokumen baru telah dibuat: ' . $this->rule->document_name,
            'action_url' => route('company-rules.show', $this->rule->id),
        ];
    }
}