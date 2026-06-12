<?php

namespace App\Exports;

use App\Models\PromotionalActivity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PromotionalActivityExport implements
    FromCollection,
    WithHeadings,
    WithEvents,
    ShouldAutoSize
{
    protected int $maxDiscussion = 0;
    protected int $maxMaterials = 0;
    protected int $maxFeedback = 0;

    public function __construct()
    {
        $activities = PromotionalActivity::all();

        foreach ($activities as $activity) {

            $discussion = json_decode($activity->discussion_Points, true) ?? [];
            $materials = json_decode($activity->material_and_Samples, true) ?? [];
            $feedback = json_decode($activity->feedback, true) ?? [];

            $this->maxDiscussion = max(
                $this->maxDiscussion,
                count($discussion)
            );

            $this->maxMaterials = max(
                $this->maxMaterials,
                count($materials)
            );

            $this->maxFeedback = max(
                $this->maxFeedback,
                count($feedback)
            );
        }
    }

    public function collection()
    {
        return PromotionalActivity::with([
            'retailer',
            'distributor',
            'approvedBy',
            'createdBy',
            'orderConfirmedBy'
        ])
        ->get()
        ->map(function ($activity) {

            $discussion = json_decode($activity->discussion_Points, true) ?? [];
            $materials = json_decode($activity->material_and_Samples, true) ?? [];
            $feedback = json_decode($activity->feedback, true) ?? [];

            $row = [
                $activity->id,
                $activity->activity_date,
                optional($activity->createdBy)->name,
                optional($activity->approvedBy)->name,
                $activity->target_market,
                $activity->product_category,
                $activity->activity_type,
                $activity->total_order_qty,
                $activity->total_order_amount,
                $activity->order_confirm_by,
                $activity->total_participants,
                optional($activity->retailer)->shop_name,
                optional($activity->distributor)->trade_name,
            ];

            // Discussion Points
            for ($i = 0; $i < $this->maxDiscussion; $i++) {
                $row[] = $discussion[$i] ?? '';
            }

            // Materials & Samples
            for ($i = 0; $i < $this->maxMaterials; $i++) {
                $row[] = $materials[$i] ?? '';
            }

            // Feedback
            for ($i = 0; $i < $this->maxFeedback; $i++) {
                $row[] = $feedback[$i] ?? '';
            }

            // Remaining columns
            $row[] = $activity->managers_remarks;
            $row[] = $activity->created_at;

            return $row;
        });
    }

    public function headings(): array
    {
        $row1 = [
            'Activity ID',
            'Activity Date',
            'Activity By Name',
            'Activity Approved By',
            'Target Market',
            'Product Displayed (Category)',
            'Activity Type',
            'Total Order Qty',
            'Total Order Amount',
            'Order Confirmed By',
            'Total Participants',
            'Retailer',
            'Distributor',
        ];

        $row2 = array_fill(0, count($row1), '');

        /*
        |--------------------------------------------------------------------------
        | Discussion Points
        |--------------------------------------------------------------------------
        */
        if ($this->maxDiscussion > 0) {

            $row1[] = 'Discussion Points';

            for ($i = 1; $i < $this->maxDiscussion; $i++) {
                $row1[] = '';
            }

            for ($i = 1; $i <= $this->maxDiscussion; $i++) {
                $row2[] = "Point {$i}";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Materials & Samples
        |--------------------------------------------------------------------------
        */
        if ($this->maxMaterials > 0) {

            $row1[] = 'Materials & Samples';

            for ($i = 1; $i < $this->maxMaterials; $i++) {
                $row1[] = '';
            }

            for ($i = 1; $i <= $this->maxMaterials; $i++) {
                $row2[] = "Material {$i}";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Feedback
        |--------------------------------------------------------------------------
        */
        if ($this->maxFeedback > 0) {

            $row1[] = 'Feedback';

            for ($i = 1; $i < $this->maxFeedback; $i++) {
                $row1[] = '';
            }

            for ($i = 1; $i <= $this->maxFeedback; $i++) {
                $row2[] = "Feedback {$i}";
            }
        }

        // Last Columns
        $row1[] = 'Manager Remarks';
        $row1[] = 'Created At';

        $row2[] = '';
        $row2[] = '';

        return [
            $row1,
            $row2
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                // Fixed columns count before Discussion Points
                $startColumn = 14; // Column N

                /*
                |--------------------------------------------------------------------------
                | Merge Discussion Points Header
                |--------------------------------------------------------------------------
                */
                if ($this->maxDiscussion > 0) {

                    $endColumn = $startColumn + $this->maxDiscussion - 1;

                    $sheet->mergeCells(
                        Coordinate::stringFromColumnIndex($startColumn) . '1:' .
                        Coordinate::stringFromColumnIndex($endColumn) . '1'
                    );

                    $startColumn = $endColumn + 1;
                }

                /*
                |--------------------------------------------------------------------------
                | Merge Materials Header
                |--------------------------------------------------------------------------
                */
                if ($this->maxMaterials > 0) {

                    $endColumn = $startColumn + $this->maxMaterials - 1;

                    $sheet->mergeCells(
                        Coordinate::stringFromColumnIndex($startColumn) . '1:' .
                        Coordinate::stringFromColumnIndex($endColumn) . '1'
                    );

                    $startColumn = $endColumn + 1;
                }

                /*
                |--------------------------------------------------------------------------
                | Merge Feedback Header
                |--------------------------------------------------------------------------
                */
                if ($this->maxFeedback > 0) {

                    $endColumn = $startColumn + $this->maxFeedback - 1;

                    $sheet->mergeCells(
                        Coordinate::stringFromColumnIndex($startColumn) . '1:' .
                        Coordinate::stringFromColumnIndex($endColumn) . '1'
                    );
                }

                // Style heading rows
                $highestColumn = $sheet->getHighestColumn();

                $sheet->getStyle("A1:{$highestColumn}2")
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle("A1:{$highestColumn}2")
                    ->getAlignment()
                    ->setHorizontal(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                    );

                $sheet->getStyle("A1:{$highestColumn}2")
                    ->getAlignment()
                    ->setVertical(
                        \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    );
            }
        ];
    }
}