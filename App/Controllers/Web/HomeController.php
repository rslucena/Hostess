<?php

declare(strict_types=1);

namespace app\Controllers\Web;

use app\Controllers\BaseController;

class HomeController extends BaseController
{
    /**
     * Main page
     * @param array $props
     * @return string
     */
    public function Start(array $props = []): string
    {
        //$this->DataBase->select();

        return self::view(
            'Web/home.index.html',
            [
                'user' => 'Rodrigo',
                'system' => ['m' => 1, 'm1' => 2, 'm2' => 3, 'm3' => 4, 'm4' => 5, 'm5' => 6],
            ]
        );
    }

    /**
     * Creates the login page
     *
     * @return void
     */
    public function Login(): void
    {
    }

    /**
     * Creates the recovery page
     *
     * @return void
     *
     */
    public function Recuperar(): void
    {
    }

    /**
     * Error page
     * 404 error
     *
     * @return void
     */
    public function Error404(): void
    {
    }
}
