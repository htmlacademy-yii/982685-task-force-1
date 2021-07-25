<?php
namespace taskforce\utils;

class CitiesCsvConverter extends AbstractCsvConverter
{
    protected function csv2sql(): string
    {
        $sql = "";

        foreach ($this->getData() as $row) {
            list($city, $lat, $long) = $row;
            
            $sql = $sql . "INSERT INTO cities (`city`, `lat`, `long`) VALUES ('$city', $lat, $long);\n";
        }

        $sql = $sql . "commit;\n";
        
        return $sql;
    }
}
