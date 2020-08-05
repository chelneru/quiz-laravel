<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class QuizSessionExport implements WithMultipleSheets
{
    private $session_id;
    private $show_correctness;
    private $exclude_incomplete;


    public function __construct(int $session_id,bool $show_correctness,bool $exclude_incomplete)
    {
        $this->session_id = $session_id;
        $this->show_correctness = $show_correctness;
        $this->exclude_incomplete = $exclude_incomplete;
    }


    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

            $sheets[] = new QuizSessionResponsesSheet($this->session_id, $this->show_correctness,$this->exclude_incomplete);
            $sheets[] = new QuizInfoSheet($this->session_id, $this->show_correctness,$this->exclude_incomplete);


        return $sheets;
    }
}
