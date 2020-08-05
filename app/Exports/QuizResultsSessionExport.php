<?php

namespace App\Exports;

use App\Services\QuizExportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuizResultsSessionExport  implements FromArray
{
    private $session_id;


    public function __construct(int $session_id )
    {
        $this->session_id = $session_id;

    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = QuizExportService::ExportSessionResults($this->session_id);
        return $rows;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Results";
    }

}
