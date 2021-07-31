<?php
namespace taskforce\utils;

class UsersCsvConverter extends AbstractCsvConverter
{
    protected function csv2sql(): string
    {
        $sql = "";
        $cityId = 755;
        $avatarId = 1;

        foreach ($this->getData() as $row) {
            list($email, $name, $password, $dateAdd, $address, $birthday, $about, $phone, $skype) = $row;
            
            $sql = $sql . "INSERT INTO users (`dt_add`, `name`, `email`, `password`, `city_id`, `birthday`, `about`, `phone`, `skype`, `avatar_id`) " .
                          "VALUES ('$dateAdd', '$name', '$email', '$password', $cityId, '$birthday', '$about', '$phone', '$skype', $avatarId);\n";
        }

        $sql = $sql . "commit;\n";

        return $sql;
    }
}
