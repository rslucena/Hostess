<?php

declare(strict_types=1);

namespace app\Middlewares;

use app\Providers\AuthProvider;
use app\Providers\LogProvider;

use CURLFile;
use DateTime;
use JsonException;

class RequestMiddleware
{
    /**
     * Creates a request to any other HTTP
     * server or to the same * in CURL
     *
     * @param $method
     * @param $end
     * @param array $props
     * @param bool $token
     * @param string $api
     *
     * @return null|array
     * @throws JsonException
     */
    public static function request($method, $end, array $props = [], bool $token = false, string $api = APP_API): ?array
    {
        //INITIALIZATION
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $api . $end);
        curl_setopt($curl, CURLOPT_TCP_FASTOPEN, true);

        //HEADER
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeader($token));

        //FILE EXIST
        if (! empty($props) && array_column($props, 'tmp_name')) {
            $method = "POST";
            curl_setopt($curl, CURLOPT_POST, true);

            foreach ($_FILES as $key => $file) {
                $array_file = $key;
                $props[$array_file] = new CURLFILE(realpath($file['tmp_name']), $file['type'], $file['name']);
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $props);
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($props, JSON_THROW_ON_ERROR));
        }

        //CONFIGS
        curl_setopt($curl, CURLOPT_ENCODING, '');

        //TIMEOUT
        curl_setopt($curl, CURLOPT_TIMEOUT, 3600);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        //OTHERS
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $response = curl_exec($curl);

        if (curl_error($curl)) {
            $response = "{ 'status' : 'error' }";
            LogProvider::save(curl_error($curl));
        }

        curl_close($curl);

        return self::buildReturn($response, [$method, $end, $props, $token]);
    }

    /**
     * Creates the header for the CURL request
     *
     * @param bool $token
     *
     * @return array
     */
    private static function getHeader(bool $token = false): array
    {
        $authorization = [];

        $head = ['Content-Type: application/json'];

        if (! empty($_FILES)) {
            $head = ['Content-Type:multipart/form-data'];
        }

        if ($token === false) {
            return $head;
        }

        if (AuthProvider::is_login()) {
            $authorization = AuthProvider::getSession('config', 'token');
        }

        if (! empty($authorization)) {
            $head[] = 'Authorization: Bearer ' . $authorization;
        }

        return $head;
    }

    /**
     * Returns an array of results
     *
     * @param string $resp
     * @param array $request
     *
     * @return array
     * @throws JsonException
     */
    public static function buildReturn($resp, $request): array
    {
        $response = [];

        $res = json_decode($resp, true);

        $response['origin'] = [];
        $response['Router'] = [];

        if (! empty($res)) {
            $response['origin'] = $res;
        }

        $response['Router'] = $request;

        if (! empty($response['origin']['access_token'])) {
            AuthProvider::auth($response['origin']['access_token'], false);
        }

        return $response;
    }

    /**
     * Retrieves all values ​​
     * sent via POST and GET
     *
     * @return null|array
     */
    public static function buildInput(): ?array
    {
        return $_REQUEST;
    }

    /**
     * Retrieves all files ​​
     * sent via FILES
     *
     * @return null|array
     */
    public static function buildFiles(): ?array
    {
        return $_FILES;
    }

    /**
     * Validates a field based
     * on its rules and value
     *
     * @param $field
     * @param $rules
     *
     * @return bool
     */
    public static function validate($field, $rules): bool
    {
        $valid = true;

        if (strpos($rules, "|") === false) {
            $rules .= "|";
        }

        $rules = explode('|', $rules);

        $rules = array_filter($rules, static function ($value) {
            return ! empty($value) || $value === 0;
        });

        if (empty($rules)) {
            return $valid;
        }

        foreach ($rules as $rule) {
            $MinMax = 0;
            $dateFormat = "Y-m-d H:i:s";

            if (
                strpos($rule, "min:") !== false ||
                strpos($rule, "max:") !== false
            ) {
                $stale = explode(":", $rule);
                $rule = $stale[0];

                if (! empty($stale[1])) {
                    $MinMax = $stale[1];
                }
            }

            if (strpos($rule, "date") !== false
            ) {
                $stale = explode(":", $rule);
                $rule = $stale[0];
                if (! empty($stale[1])) {
                    $dateFormat = $stale[1];
                }
            }

            switch ($rule) {
                case 'text':
                    $valid = is_string($field);

                    break;

                case 'number':
                    $valid = is_numeric($field);

                    break;

                case 'required':
                    $valid = ! empty($field);

                    break;

                case 'email':
                    $email = filter_var($field, FILTER_VALIDATE_EMAIL);
                    $valid = ! is_bool($email);

                    break;

                case 'min':
                    $valid = strlen($field) >= $MinMax;

                    break;

                case 'max':
                    $valid = strlen($field) <= $MinMax;

                    break;

                case 'date':

                    $date = DateTime::createFromFormat($dateFormat, $field);

                    return ! empty($date);

                    break;

                case 'equals':

                    $equal = [];

                    foreach (array_count_values($field) as $val => $c) {
                        if ($c > 1) {
                            $equal[] = $val;
                        }
                    }

                    return ! empty($equal);

                    break;

                case 'file':

                    if (! is_array($field)) {
                        return false;
                    }

                    $input = array_keys($field)[0];

                    $field = $field[$input];

                    $name = "";
                    if (! empty($field['tmp_name'])) {
                        $name = $field['tmp_name'];
                    }

                    return is_file($name);

                    break;

                default:
                    return $valid;

                    break;
            }
        }

        return $valid;
    }

    /**
     * Checks whether specific keys are contained in a
     * array array
     *
     * @param $props
     * @param array $keys
     *
     * @return bool
     */
    public static function key_exists($props, $keys = []): bool
    {
        if (! is_array($keys) || empty($props)) {
            return false;
        }

        $valid = [];

        foreach ($keys as $key) {
            $valid[] = array_key_exists($key, $props);
        }

        return ! (in_array(false, $valid, true));
    }
}
