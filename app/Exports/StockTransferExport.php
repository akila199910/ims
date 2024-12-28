<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class StockTransferExport implements FromView, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $data;
    private $count;
    private $business;

    public function __construct($data, $count, $business)
    {
        $this->data = $data;
        $this->count = $count;
        $this->business = $business;
    }

    public function view(): View
    {

        $data = $this->data;
        $count = $this->count;
        $business = $this->business;
        $stock_transfer = $this->data;

        return view('export.stockTransfer', [
            'data' => $data,
            'count' => $count,
            'business' => $business,
            'stock_transfer' => $stock_transfer
        ]);
    }

    public function registerEvents() : array
    {
        $datacount = $this->count;

        $last_row = ($datacount+8);
        return [
            AfterSheet::class    => function(AfterSheet $event) use($last_row) {

                $event->sheet->getStyle('A8:G'.$last_row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}

