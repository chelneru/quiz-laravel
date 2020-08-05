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

class QuizSessionResponsesSheet implements FromArray, WithTitle, WithHeadings
{
    private $session_id;
    private $show_correctness;
    private $exclude_incomplete;

    public function __construct(int $session_id, bool $show_correctness, bool $exclude_incomplete)
    {
        $this->session_id = $session_id;
        $this->show_correctness = $show_correctness;
        $this->exclude_incomplete = $exclude_incomplete;
    }

    public function headings(): array
    {
        $session_data = json_decode(QuizService::GetSessionQuizData($this->session_id), true);
        return QuizExportService::GenerateHeaders($session_data, $this->show_correctness);
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $rows = QuizExportService::ExportSession($this->session_id, $this->show_correctness, $this->exclude_incomplete);
        return $rows;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Responses";
    }
}