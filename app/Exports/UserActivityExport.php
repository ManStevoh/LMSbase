<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserActivityExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithStyles, ShouldAutoSize
{
    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->users;
    }
       /**
     * Start cell for data (below the title).
     */
    public function startCell(): string
    {
        return 'A2'; // Data starts from row 3
    }

    /**
     * @inheritDoc
     */
   public function headings(): array
{
    return [
        'Full Name',       // Matches 'user_name' alias
        'Email',           // Matches 'email' alias
        'Mobile',          // Assuming mobile is retrieved elsewhere
        'Role Name',       // Matches 'role' alias
        'Status',          // Assuming status is retrieved elsewhere
        'Registered At',   // Matches 'registered_at' alias
        'Last Activity',   // Matches 'last_activity' alias
        'Country',         // Matches 'country' alias
        'Operating System',// Matches 'os' alias
        'Study Time',      // Matches 'study_time' alias
        'Total Time',      // Matches 'total_time' alias
        'Courses',         // Matches 'courses' alias
        'Last Enrollment', // Matches 'last_enrollment' alias
        'Certificates',    // Matches 'certificates' alias
    ];
}


    /**
     * @inheritDoc
     */
    public function map($user): array
{
    return [
        $user->user_name,                   // Matches 'user_name' alias
        $user->email,                       // Matches 'email' alias
        $user->mobile,                      // Assuming `mobile` is retrieved elsewhere
        $user->role,                        // Matches 'role' alias
        $user->status,                      // Assuming `status` is retrieved elsewhere
        $user->registered_at,               // Matches 'registered_at' alias
        $user->last_activity,               // Matches 'last_activity' alias
        $user->country,                     // Matches 'country' alias
        $user->os,                          // Matches 'os' alias
        $user->study_time,                  // Matches 'study_time' alias
        $user->total_time,                  // Matches 'total_time' alias
        $user->courses,                     // Matches 'courses' alias
        $user->last_enrollment,             // Matches 'last_enrollment' alias
        $user->certificates,                // Matches 'certificates' alias
    ];
}
/**
     * Apply styles to the sheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Merge cells for the title
        $sheet->mergeCells('A1:N1'); // Adjust range based on the number of columns
        $sheet->setCellValue('A1', 'User Activity Report'); // Title text

        return [
            'A1' => [ // Style for the title
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

}
