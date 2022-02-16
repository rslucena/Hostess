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

    public function create(array $props): array
    {
        return [];
    }

    public function show(int $id, string $cols = '*'): array
    {
        return [];
    }

    public function update(array $filter, array $props): array
    {
        return [];
    }

    public function destroy(array $filter, array $props): array
    {
        return [];
    }
}
