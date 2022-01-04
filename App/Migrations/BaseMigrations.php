<?php

namespace app\Migrations;

class BaseMigrations
{

    public mixed $DataBase;

    public function __construct()
    {

        global $DB;
        $this->DataBase = $DB;

    }


    static function create(array $props): bool
    {

        return true;

    }

}