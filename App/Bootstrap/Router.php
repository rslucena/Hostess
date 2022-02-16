<?php

declare(strict_types=1);

namespace app\Bootstrap;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Router
 *
 * @package app
 */
class Router
{
    private static array $Listing = [];
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
    public static function ContentType(string $Type): void
    {
        $Type = ! empty($Type) ? $Type : 'text/html';
        self::$ContentType = $Type;
    }

    /**
     * Defines the method
     * used for the route.
     *
     * @param string $method
     * @return string
     */
    public static function method(string $method = ""): string
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
    public static function get(string $Name, array $Function): void
    {
        $Controller = $Function[0] ?? "";

        $Action = $Function[1] ?? "";

        if (! empty($Controller) && ! empty($Action)) {
            self::$Listing[self::$Platform]['get'][$Name] = [
                $Action,
                self::$ContentType,
                $Controller,
            ];
        }
    }

    /**
     * Defines the platform
     * used for the route.
     *
     * @param string $type
     * @return string
     */
    public static function platform(string $type = ""): string
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
    public static function post(string $Name, array $Function): void
    {
        $Controller = $Function[0] ?? "";

        $Action = $Function[1] ?? "";

        if (! empty($Controller) && ! empty($Action)) {
            self::$Listing[self::$Platform]['post'][$Name] = [
                $Action,
                self::$ContentType,
                $Controller,
            ];
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
    public static function put(string $Name, array $Function): void
    {
        $Controller = $Function[0] ?? "";

        $Action = $Function[1] ?? "";

        if (! empty($Controller) && ! empty($Action)) {
            self::$Listing[self::$Platform]['put'][$Name] = [
                $Action,
                self::$ContentType,
                $Controller, ];
        }
    }

    /**
     * Check if the path exists
     *
     * @param string $Uri
     * @param array $Parameters
     *
     * @return array
     *
     */
    #[ArrayShape(
        [
        'Status' => "bool",
        'Function' => "string[]",
        'Platform' => "string[]",
        'Message' => "string", ]
    )]
 public static function run(string $Uri, array $Parameters): array
 {
     $ReturnActions = [
            'Status' => false,
            'Function' => [
                'Name' => '',
                'Result' => '',
                'CodeResponse' => '404',
            ],
        ];

     $FunctionClass = self::$Listing[self::$Platform][self::$Method][$Uri] ?? null;

     //CALL FUNCTION
     if (! empty($FunctionClass)) {
         try {
             $ObjectClass = new $FunctionClass[2]();

             if (method_exists($ObjectClass, $FunctionClass[0])) {
                 $FunctionName = ucfirst($FunctionClass[0]);

                 $ReturnActions['Status'] = true;
                 $ReturnActions['Function']['CodeResponse'] = '200';
                 $ReturnActions['Message'] = "Action performed successfully.";

                 $ReturnActions['Function']['Name'] = $FunctionName;
                 $ReturnActions['Platform']['Content-Type'] = $FunctionClass[1];
                 $ReturnActions['Function']['Result'] = $ObjectClass->$FunctionName($Parameters[strtoupper(self::$Method)]);
             }
         } catch (Exception $exception) {
             $ReturnActions['Function'] = [
                    'Name' => $FunctionClass[2] . "\\" . ucfirst($FunctionClass[0]),
                    'CodeResponse' => '404',
                ];

             $ReturnActions['Message'] = $exception->getMessage();
         }
     }

     return $ReturnActions;
 }
}
