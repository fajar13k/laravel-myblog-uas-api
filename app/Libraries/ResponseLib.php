<?php

namespace App\Libraries;

use Illuminate\Validation\Validator;
use Throwable;

class ResponseLib
{
    public static function makeResponse(int $code, $message, $response)
    {
        $response = [
            "metaData" => [
                "code" => $code,
                "message" => $message
            ],
            "response" => $response
        ];

        return response()->json($response, $code);
    }

    public static function validationErrorResponse(Validator $validator)
    {
        $response = [
            "metaData" => [
                "code" => 400,
                "message" => "Not a valid request!",
                "validation" => $validator->errors()->toArray()
            ],
            "response" => null
        ];

        return response()->json($response, 400);
    }

    public static function errorResponse(int $code, $message, $stackTrace)
    {
        $response = [
            "metaData" => [
                "code" => $code,
                "message" => $message,
                "stackTrace" => collect($stackTrace)
            ],
            "response" => null
        ];

        return response()->json($response, $code);
    }

    public static function exceptionResponse(Throwable $exception, int $code = null)
    {
        $response = [
            "metaData" => [
                "code" => $code ?? $exception->getCode(),
                "message" => $exception->getMessage(),
                "trace" => "{$exception->getFile()} at line {$exception->getLine()}"
            ],
            "response" => null
        ];

        return response()->json($response, $code ?? 400);
    }

    public static function successResponse($response)
    {
        return static::makeResponse(200, "OK", $response);
    }

    public static function notFoundResponse()
    {
        return static::makeResponse(404, "Route not found!", null);
    }
}
