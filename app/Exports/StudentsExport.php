<?php
namespace App\Exports;

use App\Http\Controllers\Web\traits\UserFormFieldsTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    use UserFormFieldsTrait;

    protected $users;
    protected $currency;
    protected $form;

    public function __construct($users)
    {
        $this->users = $users;
        $this->currency = currencySign();
        $this->form = $this->getFormFieldsByType("user");
    }

    public function collection()
    {
        return $this->users;
    }

    public function headings(): array
    {
        $items = [
            'ID',
            'First Name',
            'Middle Name',
            'Last Name',
            'Affiliate Name',
            'Affiliate Code',
            'Mobile',
            'Email',
            'Classes',
            'Appointments',
            'Wallet Charge',
            'User Group',
            'Register Date',
            'Status',
            'Gender',
            'Country',
            'State',
            'LGA',
        ];

        if (!empty($this->form)) {
            foreach ($this->form->fields as $field) {
                $items[] = $field->title;
            }
        }

        return $items;
    }

    public function map($user): array
    {
        $affiliateUser = optional(optional($user->referredBy)->affiliateUser);
        $affiliateName = $affiliateUser ? $affiliateUser->full_name : 'N/A';
        $affiliateCode = optional($affiliateUser->affiliateCode)->code ?? 'N/A';

        $items = [
            $user->id,
            $user->full_name,
            $user->middle_name,
            $user->last_name,
            $affiliateName,
            $affiliateCode,
            $user->mobile,
            $user->email,
            $user->classesPurchasedsCount . ' (' . $this->currency . $user->classesPurchasedsSum . ')',
            $user->meetingsPurchasedsCount . ' (' . $this->currency . $user->meetingsPurchasedsSum . ')',
            $this->currency . $user->getAccountingBalance(),
            !empty($user->userGroup) ? $user->userGroup->group->name : '',
            dateTimeFormat($user->created_at, 'j M Y - H:i'),
            $user->status,
            $user->gender,
            $user->country,
            $user->state,
            $user->lga,
        ];

        if (!empty($this->form)) {
            $items = $this->handleFieldsForExport($this->form, $user, $items);
        }

        return $items;
    }
}
