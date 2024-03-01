<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExcelExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $headings;
    protected $data;

    public function __construct(array $headings, array $data)
    {
        $this->headings = $headings;
        $this->data = $data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->data;
    }
}