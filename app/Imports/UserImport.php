<?php


namespace App\Imports;


use App\Services\ClassService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UserImport implements ToCollection
{
    private $class_id;

    public function __construct(int $class_id)
    {
        $this->class_id = $class_id;

    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (isset($row[0], $row[1], $row[2])) {
                ClassService::ManuallyInviteParticipants($this->class_id, [$row[0]], [$row[1]], [$row[2]]);
            }
        }
    }
}
