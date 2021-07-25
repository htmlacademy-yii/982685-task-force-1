<?php
namespace taskforce\utils;

class ReviewsCsvConverter extends AbstractCsvConverter
{
    protected function csv2sql(): string
    {
        $sql = "";
        $taskId = 1;
        $executorId = 2;

        foreach ($this->getData() as $row) {
            list($dateAdd, $rate, $description) = $row;
            
            $sql = $sql . "INSERT INTO reviews (`dt_add`, `eval`, `review_text`, `task_id`, `executor_id`) " .
                          "VALUES ('$dateAdd', $rate, '$description', $taskId, $executorId);\n";
        }

        $sql = $sql . "commit;\n";

        return $sql;
    }
}
