<?php

declare(strict_types=1);

namespace app\Controllers\Api;

use app\Controllers\BaseController;
use app\Providers\ValidatorProviders;

class CustomerController extends BaseController
{

    /**
     * Creates the login page
     *
     * @param array $Props
     *
     * @return string
     */
    public function CreateLogin(array $Props ): string
    {

        $Roles = array(
            'current-user' => 'required|string|min:3|max:50',
            'current-password' => 'email|max:255'
        );

        $IsValid = ValidatorProviders::Make($Props,$Roles)->withErrors();

        var_dump($IsValid);

        return "";
    }

}
