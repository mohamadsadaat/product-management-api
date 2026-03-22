<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse(string $message, $data = null, int $code = 200, array $extra = [])
    {
        return response()->json(array_merge([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $extra), $code);
    }

    protected function errorResponse(string $message, int $code = 400, $errors = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
