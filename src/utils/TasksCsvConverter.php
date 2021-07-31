<?php
namespace taskforce\utils;

class TasksCsvConverter extends AbstractCsvConverter
{
    protected function csv2sql(): string
    {
        $sql = "";
        $cityId = 755;
        $locationId = 1;

        foreach ($this->getData() as $row) {
            list($dateAdd, $categoryId, $description, $expire, $name, $address, $budget, $lat, $long) = $row;
            
            $customerId = rand(1, 20);
            
            $sql = $sql . "INSERT INTO locations (`city_id`, `lat`, `long`) VALUES ($cityId, $lat, $long);\n";
            $sql = $sql . "commit;\n";
            
            $sql = $sql . "INSERT INTO tasks (`dt_add`, `job_essence`, `job_details`, `expire`, `budget`, `location_id`, `category_id`, `customer_id`) " .
                          "VALUES ('$dateAdd', '$name', '$description', '$expire', $budget, $locationId, $categoryId, $customerId);\n";
            $sql = $sql . "commit;\n";
            
            $locationId++;
        }

        return $sql;
    }
}
