<?php
declare(strict_types=1);

namespace app\Bootstrap;

use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Router
 *
 * @package app
 */
class Router
{

    private static array $Listing = array();
    private static string $Platform = 'Web';
    private static string $Method = 'get';
    private static string $ContentType = 'text/html';

    /**
     * Define a content return type
     * Known as Content-type
     *
     * @param string $Type
     *
     */
    static function ContentType(string $Type): void
    {
        $Type = !empty($Type) ? $Type : 'text/html';
        self::$ContentType = $Type;
    }

    /**
     * Defines the method
     * used for the route.
     *
     * @param string $method
     * @return string
     */
    static function method(string $method = ""): string
    {
        self::$Method = empty($method) ? 'get' : $method;
        return self::$Method;
    }

    /**
     * Set a new route of type GET
     * to a directory
     *
     * @param string $Name
     * @param array $Function
     *
     */
    static function get(string $Name, array $Function): void
    {

        $Controller = $Function[0] ?? "";

        $Action = $Function[1] ?? "";

        if (!empty($Controller) || !empty($Action)) {
            self::$Listing[self::$Platform][$Controller]['get'][$Name] = array($Action, self::$ContentType);
        }

    }

    /**
     * Defines the platform
     * used for the route.
     *
     * @param string $type
     * @return string
     */
    static function platform(string $type = ""): string
    {
        self::$Platform = empty($type) ? 'Web' : $type;
        return self::$Platform;
    }

    /**
     * Set a new route of type POST
     * to a directory
     *
     * @param string $Name
     * @param array $Function
     *
     */
    static function post(string $Name, array $Function): void
    {

        $Controller = $Function[0] ?? "";

        $Action = $Function[1] ?? "";

        if (!empty($Controller) || !empty($Action)) {
            self::$Listing[self::$Platform][$Controller]['post'][$Name] = array($Action, self::$ContentType);
        }

    }

    /**
     * Set a new route of type POST
     * to a directory
     *
     * @param string $Name
     * @param array $Function
     *
     */
    static function put(string $Name, array $Function): void
    {

        $Controller = $Function[0] ?? "";

        $Action = $Function[1] ?? "";

        if (!empty($Controller) || !empty($Action)) {
            self::$Listing[self::$Platform][$Controller]['put'][$Name] = array($Action, self::$ContentType);
        }

    }

    /**
     * Check if the path exists
     *
     * @param string $Class
     * @param string $Uri
     * @param array $Parameters
     *
     * @return array
     *
     */
    #[ArrayShape([
        'Status' => "bool",
        'Function' => "string[]",
        'Platform' => "string[]",
        'Message' => "string"]
    )] public static function run(string $Class, string $Uri, array $Parameters): array
    {

        $ReturnActions = array(
            'Status' => false,
            'Function' => array(
                'Name' => '',
                'Result' => '',
                'CodeResponse' => '404'
            ),
            'Platform' => array(
                'Content-Type' => ''
            ),
            'Message' => ''
        );

        $FunctionClass = self::$Listing[self::$Platform][$Class][self::$Method][$Uri] ?? null;

        //CALL FUNCTION
        if (class_exists($Class) && (!empty($FunctionClass) && $FunctionClass[0] !== null)) {

            $ReturnActions['Status'] = true;
            $ReturnActions['Message'] = "Action performed successfully.";

            $ObjectClass = new $Class();

            if (method_exists($ObjectClass, $FunctionClass[0])) {

                $FunctionName = $FunctionClass[0];

                $ReturnActions['Function']['CodeResponse'] = '200';
                $ReturnActions['Function']['Name'] = $FunctionName;
                $ReturnActions['Platform']['Content-Type'] = $FunctionClass[1];
                $ReturnActions['Function']['Result'] = $ObjectClass->$FunctionName($Parameters[strtoupper(self::$Method)]);

            }

        }

        return $ReturnActions;

    }

}

