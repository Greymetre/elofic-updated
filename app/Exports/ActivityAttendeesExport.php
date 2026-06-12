<?php

namespace App\Exports;

use App\Models\ActivityAttendee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActivityAttendeesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ActivityAttendee::select(
            'id',
            'activity_id',
            'person_name',
            'contact_no',
            'address',
            'remarks',
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Activity ID',
            'Person Name',
            'Contact No',
            'Address',
            'Remarks',
            'Created At'
        ];
    }
}