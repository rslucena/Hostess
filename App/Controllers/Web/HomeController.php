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
    public function start(array $props = array()): string
    {

        $this->DataBase->select();

        return self::view(
            'Web/home.index.html',
            array(
                'user' => 'Rodrigo',
                'system' => array('m' => 1, 'm1' => 2, 'm2' => 3, 'm3' => 4, 'm4' => 5, 'm5' => 6)
            ));


    }

    /**
     * Creates the login page
     *
     * @return void
     */
    public function login(): void
    {
    }

    /**
     * Creates the recovery page
     *
     * @return void
     *
     */
    public function recuperar(): void
    {
    }

    /**
     * Error page
     * 404 error
     *
     * @return void
     */
    public function error404(): void
    {

    }

}
