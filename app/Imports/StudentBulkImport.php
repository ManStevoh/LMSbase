<?php

namespace App\Imports;

use App\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Jobs\StudentBulkUploadEmailNotification;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterImport;

class StudentBulkImport implements ToModel, WithStartRow, WithEvents
{
    protected $emailData = [];

    public function __construct()
    {
        $this->emailData = [];
    }

    // Specify the row number to start reading the data (4th row)
    public function startRow(): int
    {
        return 3;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $this->sendEmails();
            },
        ];
    }

    public function sendEmails()
    {
        foreach ($this->emailData as $email) {
            Log::info('Sending email to: ' . $email['email']);
            dispatch_sync(new StudentBulkUploadEmailNotification(
                $email['email'],
                $email['name'],
                $email['login_url'],
                $email['password']
            ));
            Log::info('Email sent successfully to: ' . $email['email']);
        }
    }

    public function model(array $row)
    {
        // Skip empty rows (both first and second columns empty)
        if (empty($row[0]) && empty($row[1])) {
            return null;
        }

        $exists = User::where('email', $row[3])->first();
        if($exists){
            return null;
        }

        try {
            DB::beginTransaction();
            // Handle tenant creation if necessary
            $password = bin2hex(random_bytes(3));
            $user = User::create([
                'full_name' => $row[0],
                'middle_name' => $row[1],
                'last_name' => $row[2],
                'email' => $row[3],
                'mobile' => $row[4],
                'role_name' => 'user',
                'role_id' => 1,
                'password' => Hash::make($password),
                'status' => 'active',
                'verified' => true,
                'created_at' => time(),
                'password_status' => false
            ]);

            // Add email data to the array for batch processing after import
            if ($user) {
                $this->emailData[] = [
                    'email' => $user->email,
                    'name' => $user->full_name,
                    'login_url' => route('login'),
                    'password' => $password,
                ];
            }

            DB::commit();
            Log::info('User created successfully: ' . $user->email);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing row: ' . $e->getMessage());
            throw new \Exception("Error processing the row: " . $e->getMessage());
        }

        return $user;
    }
}
