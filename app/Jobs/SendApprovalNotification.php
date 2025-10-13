<?php

namespace App\Jobs;

use App\Models\CompanyRule;
use App\Models\User;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendApprovalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $rule;

    protected $message;

    protected $actionUrl;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, CompanyRule $rule, string $message, string $actionUrl)
    {
        $this->user = $user;
        $this->rule = $rule;
        $this->message = $message;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->user->notify(new DocumentStatusUpdated($this->rule->id, $this->message, $this->actionUrl));
    }
}
