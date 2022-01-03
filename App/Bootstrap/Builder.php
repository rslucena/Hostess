<?php

declare(strict_types=1);

namespace app\Bootstrap;

use app\Controllers\Api\CustomerController;
use app\Controllers\Web\HomeController;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Builder
 *
 * Responsible for defining routes, running controllers and Middleware
 * @package app\Bootstrap
 */
class Builder
{

    /**
     * Route Builder
     * @return void
     */
    static public function Routes(): void
    {

        //Set:Web
        Router::ContentType('text/html');
        Router::get('/', [HomeController::class, 'start']);
        Router::get('/login', [HomeController::class, 'login']);

        //Set:Api
        Router::platform('Api');
        Router::ContentType('application/json');
        Router::post('/customer/login', [CustomerController::class, 'CreateLogin']);


        //Set:Cli
        Router::ContentType('text/html');
        Router::platform('Cli');

        //Router::put('clientes/avatar', array());

    }

    /**
     * Render the View
     * and its variables
     *
     * @param string $File
     * @param array $Props
     *
     * @return string
     *
     */
    static public function view(string $File, array $Props): string
    {

        $CurrentFile = "";
        $Path = PATH_ROOT . DIR_VIEW . DIRECTORY_SEPARATOR . $File ;

        if (file_exists($Path) === false) {
            return $CurrentFile;
        }

        $CurrentFile = file_get_contents($Path);

        preg_match_all('/({)(.*?)(})/', $CurrentFile, $SequenceKeys, PREG_PATTERN_ORDER);

        $SequenceKeys = $SequenceKeys[0] ?? array();

        if (empty($SequenceKeys)) {
            return $CurrentFile;
        }

        foreach ($SequenceKeys as $Values) {

            $Reference = explode('.', str_replace(array('{', '}'), '', $Values));
            $NeedSearch = $Props[$Reference[0]];
            unset($Reference[0]);

            if (is_array($NeedSearch)) {

                foreach ($Reference as $Key) {
                    $NeedSearch = $NeedSearch[$Key] ?? "";
                }

            }

            $CurrentFile = str_replace($Values, (string)$NeedSearch, $CurrentFile);

        }

        return $CurrentFile;

    }

    /**
     * Performs a function based on the requisition structure
     * Formatting all the variables needed by the controller.
     *
     * @param object $Request
     *
     * @return array
     */
    #[ArrayShape([
        'Class' => "array|string|string[]",
        'User' => "array",
        'Platform' => "array",
        'Status' => "false|string",
        'Function' => "array",
        'Parameters' => "array",
        'Redirect' => "array",
        'Message' => "string"])]
    static function execute(object $Request): array
    {

        $Header = $Request->header();
        $UserAgent = strtolower($Header['user-platform-agent'] ?? $Header['user-agent'] ?? "cli/command");

        $Platform = match (true) {
            str_contains($UserAgent, "mobile") => 'Api',
            str_contains($UserAgent, "cli") => 'Cli',
            default => 'Web',
        };

        $Reference = explode('/', $Request->uri() ) ?? array();

        $ControllerName = !empty($Reference[1]) ? $Reference[1] : "Home";
        $ControllerName = DIR_CONTROL . "\\" . $Platform . "\\". $ControllerName . 'Controller';
        $ControllerName = substr_replace($ControllerName, "app", 0, 4);

        $ActionName = strtolower(!empty($Reference[2]) ? $Reference[2] : "Index");

        //Structure:Return
        $ReturnActions = array(

            'Class' => $ControllerName,

            'Header' => array(
                'Host' => $Request->host(),
                'ProtocolVersion' => $Request->protocolVersion()
            ),

            'User' => array(
                'Logged' => false,
                'Token' => "",
                'Info' => array()
            ),

            'Platform' => array(
                'Mode' => Router::platform($Platform),
                'Method' => Router::method( strtolower($Request->method()) ),
                'Content-Type' => "",
                'IsFile' => pathinfo($Request->uri(), PATHINFO_EXTENSION) !== ""
            ),

            'Status' => false,

            'Function' => array(
                'Uri' => pathinfo($Request->uri()),
                'Action' => $ActionName,
                'Name' => "",
                'Result' => "",
                'CodeResponse' => "404",
            ),

            'Parameters' => array(
                'GET' => $Request->get(),
                'POST' => $Request->post(),
                'Query' => $Request->queryString(),
                'Files' => $Request->file()
            ),

            'Redirect' => array(
                'force' => false,
                'location' => ""
            ),

            'Message' => "Method not allocated for application.",

        );

        if( $ReturnActions['Platform']['IsFile'] ){
            return $ReturnActions;
        }

        return array_merge($ReturnActions, Router::run($ControllerName, strtolower($Request->uri()), $ReturnActions['Parameters']));

    }

}