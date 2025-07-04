<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class StudentBulkEnrollmentEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantEmail;
    public $tenantName;
    public $link;
    public $courses;
    /**
     * Create a new job instance.
     */
    public function __construct($tenantEmail, $tenantName, $link, $courses)
    {
        $this->tenantEmail = $tenantEmail;
        $this->tenantName = $tenantName;
        $this->link = $link;
        $this->courses = $courses;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Compose email content
        $emailContent = "
            <p>Dear {$this->tenantName},</p>
            <p>We’re thrilled to let you know that you’ve been successfully enrolled in the following course(s) on " . env('APP_NAME') . ":</p>
            <p>You can now access the platform to explore a world of learning opportunities tailored just for you.</p>
            <p><strong>Courses:</strong></p>
            <ul>
        ";
        
        foreach ($this->courses as $course) {
            $emailContent .= "<li><strong>{$course}</strong></li>";
        }
        
        $emailContent .= "
            </ul>
            <p>Click here to log into your account:</p>
            <p><a href='{$this->link}'>LOGIN</a></p>
            <p>If you have any questions or face any difficulties, our support team is always here to help.</p>
            <p>Happy Learning,<br>" . env('APP_NAME') . " Team</p>
        ";

        // Send the email
        Mail::html($emailContent, function ($message) {
            $message->to($this->tenantEmail)
                ->subject('You’ve Been Enrolled in a New Course on '.env('APP_NAME'));
        });

    }
}

