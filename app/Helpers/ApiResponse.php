<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success($data = null, $message = "Operation successful", $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error($message = "An error occurred", $errors = null, $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
