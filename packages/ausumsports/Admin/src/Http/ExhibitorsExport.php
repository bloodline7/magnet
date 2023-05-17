<?php

namespace Ausumsports\Admin\Http;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Ausumsports\Admin\Http\Convention;


class ExhibitorsExport implements FromCollection, WithHeadings , ShouldAutoSize, WithStyles, WithColumnFormatting
{

    public function map($Result): array
    {
        return [

            Date::dateTimeToExcel($Result->created_at),
            Date::dateTimeToExcel($Result->updated_at),
        ];
    }


    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }


    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_DATE_DATETIME,
           // 'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Email',
            'Company',
            'Site',
            'Phone',
            'Zip code',
            'Address',
            'City',
            'State',
            'Created At',
            'Updated At',
        ];
    }


    public function collection()
    {

        $Convention = new Convention();
        $Q = $Convention->ExhibitorsSearch(request());

        $Q = $Q->where('type', 'exhibitors');
        $Q = $Q->select('id', 'email' ,  'company', 'site' , 'phone' , 'zip', 'address' , 'city' , 'state' , 'created_at', 'updated_at');


        return $Q->orderBy('id', 'asc')->get();

    }
}
