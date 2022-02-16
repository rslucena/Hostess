<?php

namespace app\Providers;

class ValidatorProviders
{
    private static array $Errors = [];

    /**
     * Create validator
     * for passed parameters
     *
     * @param array $Props
     * @param array $Roles
     * @return object
     */
    public static function Make(array $Props, array $Roles): object
    {
        $Roles = array_map(function ($Key) {
            return explode('|', $Key);
        }, $Roles);

        foreach ($Roles as $Keys => $Role) {
            foreach ($Role as $Key => $Function) {
                $NumberLimit = 0;
                $NameFunction = ucfirst($Function);

                if (str_contains(":", $Function)) {
                    $NameFunction = ucfirst($Function[0]);
                    $NumberLimit = $Function[1];
                }

                $Props[$Keys][$Key] = self::$NameFunction($Props[$Keys], $NumberLimit);
            }
        }

        return (object)[
            'Props' => $Props,
            'Roles' => $Roles,
        ];
    }

    /**
     * Return existing errors
     * in validation
     *
     * @return array
     *
     */
    public static function withErrors(): array
    {
        foreach (self::$Errors as $key => $error) {
            if (empty($error)) {
                unset(self::$Errors[$key]);
            }
        }

        return self::$Errors;
    }

    /**
     * Validates if there is a defined
     * value for the variable
     *
     * @param mixed|null $Value
     *
     * @return array
     *
     */
    private static function Required(mixed $Value): array
    {
        return ! empty($Value) ? [] : ['Empty mandatory field.'];
    }

    /**
     * Checks if the value
     * is really a string
     *
     * @param mixed|null $Value
     * @return array
     */
    private static function String(mixed $Value): array
    {
        return is_string($Value) ? [] : ['The field is not text.'];
    }

    /**
     * Checks if the value contains a minimum
     * number of characters
     *
     * @param int $Min
     * @param string $Value
     * @return array
     */
    private static function Min(string $Value, int $Min): array
    {
        return ! empty($Value) && strlen($Value) > $Min ? [] : ['The value does not contain the expected minimum.'];
    }

    /**
     * Checks if the value contains a maximum
     * number of characters
     *
     * @param int $Max
     * @param string $Value
     * @return array
     */
    private static function Max(string $Value, int $Max): array
    {
        return ! empty($Value) && strlen($Value) <= $Max ? [] : ['This value exceeds the limit stipulated for the field.'];
    }

    /**
     * Check if the value is an email
     *
     * @param string $Value
     * @return array
     */
    private static function Email(string $Value): array
    {
        return filter_var($Value, FILTER_VALIDATE_EMAIL) ? [] : ['No valid email was found.'];
    }

    /**
     * Check if the value is a number
     *
     * @param mixed $Value
     * @return array
     */
    private static function Number(mixed $Value): array
    {
        return is_numeric("") ? [] : ['It is not a numerical value.'];
    }
}
