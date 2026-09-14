<?php

namespace App\Exports;

use App\Services\UserMonthlyAnalysisService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UserMonthlyAnalysisExport implements FromArray, ShouldAutoSize, WithEvents
{
    private array $report;

    public function __construct(string $startDate, string $endDate, ?int $userId = null)
    {
        $this->report = app(UserMonthlyAnalysisService::class)->build($startDate, $endDate, $userId);
    }

    public function array(): array
    {
        $header1 = ['EMPLOYEE', 'EMPLOYEE NAME', 'DESIG.'];
        $header2 = ['', '', ''];
        foreach ($this->report['dates'] as $date) {
            $header1 = array_merge($header1, [date('d-M', strtotime($date)), '', '']);
            $header2 = array_merge($header2, ['Mech.', 'Ret.', 'Dist.']);
        }
        $header1 = array_merge($header1, ['TOTAL', '', '', 'WORKING DAY', 'AVG.', '', '']);
        $header2 = array_merge($header2, ['Mech.', 'Ret.', 'Dist.', '', 'Mech.', 'Ret.', 'Dist.']);

        $rows = [$header1, $header2];
        foreach ($this->report['rows'] as $row) {
            $values = [$row['employee_code'], $row['name'], $row['designation']];
            foreach ($this->report['dates'] as $date) {
                $day = $row['days'][$date];
                if ($day['status'] === 'P') {
                    $values = array_merge($values, [
                        (string) $day['mechanic'],
                        (string) $day['retailer'],
                        (string) $day['distributor'],
                    ]);
                } elseif ($day['status'] === 'A') {
                    $values = array_merge($values, ['A', 'A', 'A']);
                } else {
                    $values = array_merge($values, [$day['status'], '', '']);
                }
            }
            $values = array_merge($values, array_values($row['totals']), [$row['working_days']], array_values($row['averages']));
            $rows[] = $values;
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $dateCount = count($this->report['dates']);
            $lastColumnNumber = 3 + ($dateCount * 3) + 7;
            $lastColumn = Coordinate::stringFromColumnIndex($lastColumnNumber);

            foreach ([1, 2, 3] as $column) {
                $letter = Coordinate::stringFromColumnIndex($column);
                $sheet->mergeCells("{$letter}1:{$letter}2");
            }
            for ($i = 0; $i < $dateCount; $i++) {
                $start = 4 + ($i * 3);
                $sheet->mergeCells(Coordinate::stringFromColumnIndex($start) . '1:' . Coordinate::stringFromColumnIndex($start + 2) . '1');
            }
            $totalsStart = 4 + ($dateCount * 3);
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($totalsStart) . '1:' . Coordinate::stringFromColumnIndex($totalsStart + 2) . '1');
            $workingColumn = Coordinate::stringFromColumnIndex($totalsStart + 3);
            $sheet->mergeCells("{$workingColumn}1:{$workingColumn}2");
            $sheet->mergeCells(Coordinate::stringFromColumnIndex($totalsStart + 4) . '1:' . Coordinate::stringFromColumnIndex($totalsStart + 6) . '1');

            $sheet->freezePane('D3');
            $sheet->setAutoFilter("A2:{$lastColumn}2");
            $sheet->getStyle("A1:{$lastColumn}2")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00AADB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle('A1:' . $lastColumn . $sheet->getHighestRow())->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'B7B7B7']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            for ($row = 3; $row <= $sheet->getHighestRow(); $row++) {
                for ($column = 4; $column < $totalsStart; $column += 3) {
                    $cell = Coordinate::stringFromColumnIndex($column) . $row;
                    if ($sheet->getCell($cell)->getValue() === 'A') {
                        $endCell = Coordinate::stringFromColumnIndex($column + 2) . $row;
                        $sheet->getStyle("{$cell}:{$endCell}")->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                        $sheet->getStyle("{$cell}:{$endCell}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    } elseif ($sheet->getCell($cell)->getValue() === 'L') {
                        $sheet->mergeCells($cell . ':' . Coordinate::stringFromColumnIndex($column + 2) . $row);
                        $sheet->getStyle($cell)->getFont()->setBold(true)->getColor()->setRGB('FF0000');
                        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }
            }
        }];
    }
}
