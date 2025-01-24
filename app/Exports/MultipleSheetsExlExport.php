<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class MultipleSheetsExlExport implements WithMultipleSheets 
{
    use WithConditionalSheets;

    public function conditionalSheets(): array
    {
        return [
            'Worksheet 0' => new BookstockExportExcel(),
            'Worksheet 1' => new AicenterExlExport(),
        ];
    }
}



