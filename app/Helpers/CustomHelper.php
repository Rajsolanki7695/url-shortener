<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CustomHelper
{
    public static function SuccessResponse($message, $data, $newKey = '')
    {
        $payload = [
            'responseCode' => 1,
            'responseText' => $message,
            'responseData' => $data,
        ];

        if (is_array($newKey) && count($newKey) > 0) {
            foreach ($newKey as $key => $value) {
                $payload[$key] = !is_array($value) ? (string) $value : $value;
            }
        }

        return response()->json($payload);
    }

    public static function SuccessWithoutData($message, $newKey = '')
    {
        $payload = [
            'responseCode' => 1,
            'responseText' => $message,
        ];

        if (is_array($newKey) && count($newKey) > 0) {
            foreach ($newKey as $key => $value) {
                $payload[$key] = (string) $value;
            }
        }

        return response()->json($payload);
    }

    public static function ErrorResponse($message, $newKey = '')
    {
        $payload = [
            'responseCode' => 0,
            'responseText' => $message,
        ];

        if (is_array($newKey) && count($newKey) > 0) {
            foreach ($newKey as $key => $value) {
                $payload[$key] = (string) $value;
            }
        }

        return response()->json($payload);
    }

    public static function capitalizeKeys($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $result = [];
        foreach ($data as $key => $value) {
            $parts = explode('_', (string) $key);
            $newKey = $parts[0];
            for ($i = 1; $i < count($parts); $i++) {
                $newKey .= ucfirst($parts[$i]);
            }
            $result[$newKey] = self::capitalizeKeys($value);
        }

        return $result;
    }

    public static function convertToString($value)
    {
        if (is_array($value)) {
            return array_map([self::class, 'convertToString'], $value);
        }

        if (is_string($value) && !is_numeric($value)) {
            return Str::ucfirst($value);
        }

        return (string) $value;
    }

    public static function CapitalizeArray($data)
    {
        $capitalized = self::capitalizeKeys($data);
        return self::convertToString($capitalized);
    }
}
