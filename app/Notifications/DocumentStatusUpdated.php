<?php

namespace App\Notifications;

use App\Models\CompanyRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class DocumentStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $companyRuleId;

    protected $message;

    protected $actionUrl;

    public function __construct(int $companyRuleId, string $message, string $actionUrl)
    {
        $this->companyRuleId = $companyRuleId;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $companyRule = CompanyRule::find($this->companyRuleId);
        if (! $companyRule) {
            return (new MailMessage)->line('The referenced document could not be found.');
        }

        return (new MailMessage)
            ->subject('Document Approval Request: '.$companyRule->document_name)
            ->greeting('Hello, '.$notifiable->name.'!')
            ->line($this->message)
            ->line('Document Name: '.$companyRule->document_name)
            ->line('Created by: '.$companyRule->creator->name)
            ->action('View Document', route('company-rules.show', $companyRule->id))
            ->line('Thank you for your attention.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        Log::info('Attempting to create database notification for user ID: '.$notifiable->id);

        $companyRule = CompanyRule::find($this->companyRuleId);

        if (! $companyRule) {
            $data = [
                'document_id' => $this->companyRuleId,
                'document_name' => 'Unknown Document (may have been deleted)',
                'message' => $this->message,
                'action_url' => $this->actionUrl,
            ];
            Log::warning('CompanyRule not found for ID: '.$this->companyRuleId.'. Using fallback data for notification.');
            Log::info('Notification data for user ID '.$notifiable->id.': '.json_encode($data));

            return $data;
        }

        $data = [
            'document_id' => $companyRule->id,
            'document_name' => $companyRule->document_name,
            'message' => $this->message,
            'action_url' => $this->actionUrl,
        ];

        Log::info('Notification data for user ID '.$notifiable->id.': '.json_encode($data));

        return $data;
    }
}
