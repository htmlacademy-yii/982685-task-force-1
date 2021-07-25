<?php
namespace taskforce\utils;

class CategoriesCsvConverter extends AbstractCsvConverter
{
    protected function csv2sql(): string
    {
        $sql = "";
        
        $sql = $sql . "INSERT INTO files (`path`) VALUES ('img/dummy_avatar.png');\n";  // ID = 1
        $sql = $sql . "commit;\n";
        
        $filesId = 2;

        foreach ($this->getData() as $row) {

            list($name, $icon) = $row;

            $fileIconPath = "img/" . $icon . ".png";
            
            $sql = $sql . "INSERT INTO files (`path`) VALUES ('$fileIconPath');\n";
            $sql = $sql . "commit;\n";
            $sql = $sql . "INSERT INTO categories (`category_name`, `icon_id`) VALUES ('$name', $filesId);\n";
            $sql = $sql . "commit;\n";

            $filesId++;
        }

        return $sql;
    }
}
