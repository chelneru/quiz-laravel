<?php
/**
 * Created by PhpStorm.
 * User: au614698
 * Date: 21-03-2019
 * Time: 15:03
 */

namespace App\Exports;


use App\Services\QuizExportService;
use App\Services\QuizService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class QuizInfoSheet implements FromArray, WithTitle
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
        $rows = QuizExportService::ExportSessionQuizInfo($this->session_id);
        return $rows;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Quiz Info";
    }
}