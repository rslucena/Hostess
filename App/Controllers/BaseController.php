<?php

namespace app\Controllers;

use app\Bootstrap\Builder;

class BaseController extends Builder
{

    public mixed $DataBase;

    public function __construct()
    {

        global $DB;
        $this->DataBase = $DB;

    }

    function create(array $props): array
    {

        return array();

    }

    function show( int $id, string $cols = '*' ): array
    {
        return array();
    }

    function update(array $filter, array $props): array
    {
        return array();
    }

    function destroy(array $filter, array $props): array
    {

        return array();
    }

}