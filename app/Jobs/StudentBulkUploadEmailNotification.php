<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class StudentBulkUploadEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantEmail;
    public $tenantName;
    public $link;
    public $password;
    /**
     * Create a new job instance.
     */
    public function __construct($tenantEmail, $tenantName, $link, $password)
    {
        $this->tenantEmail = $tenantEmail;
        $this->tenantName = $tenantName;
        $this->link = $link;
        $this->password = $password;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Compose email content
        $emailContent = "
                <p>Dear {$this->tenantName},</p>
                <p>Following from our previous email, we are pleased to inform you that the " . env('APP_NAME') . "has completed the upgrade to version 3.0 and your account has been successfully migrated. Welcome to a world of new possibilities.</p>
                <p>Here are your login details:</p>
                <ul>
                    <li><strong>Email:</strong> {$this->tenantEmail}</li>
                    <li><strong>Password:</strong> {$this->password}</li>
                </ul>
                <p>For your security, you will be asked to change your password upon your first login.</p>
                <p>Click here to log in:</p>
                <p><a href='{$this->link}'>LOGIN</a></p>
                <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
                <p>Happy Learning,<br>" .env('APP_NAME')." Team</p>
            ";
        // Send the email
        Mail::html($emailContent, function ($message) {
            $message->to($this->tenantEmail)
                ->subject('Your Account has Been Migrated, Change your Password.');
        });

    }
}

