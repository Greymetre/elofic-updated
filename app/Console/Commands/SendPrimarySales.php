<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\PrimarySale;
use App\Models\PrimarySales;

class SendPrimarySales extends Command
{
    protected $signature = 'sales:send-primary';
    protected $description = 'Send primary sales data to external API';

    public function handle()
    {
        $geturl = "https://dashboard.fieldkonnect.io/power-bi/public/api/getPrimarySalesLastId";
        $getresponse = Http::timeout(240)->get($geturl);

        if (!$getresponse->successful()) {
            $this->error('Failed to fetch last sent ID.');
            return;
        }

        $lastId = $getresponse->json();
        $MylastId = PrimarySales::whereNotNull('id')->max('id');

        if ($lastId >= $MylastId) {
            $this->info('No new sales data to send.');
            return;
        }

        $url = "https://dashboard.fieldkonnect.io/power-bi/public/api/insertPrimarySales";

        $salesData = PrimarySales::select(
            'id as main_id',
            'invoiceno',
            'invoice_date',
            'month',
            'division',
            'dealer',
            'city',
            'state',
            'final_branch',
            'sales_person',
            'model_name',
            'product_name',
            'quantity',
            'rate',
            'net_amount',
            'total_amount',
            'group_name',
            'branch',
            'created_at',
            'updated_at'
        )
            ->orderBy('id', 'desc')
            ->limit(20000)
            ->get();

        $salesData->chunk(200)->each(function ($chunk) use ($url) {
            $payload = ['sales' => $chunk->toArray()];
            $response = Http::timeout(240)->post($url, $payload);

            if ($response->successful()) {
                $this->info(count($chunk) . ' records sent successfully.');
            } else {
                $this->error('Failed to send sales data: ' . $response->body());
            }
        });

        $this->info('All primary sales data processed.');
    }
}
