<?php

namespace App\Traits;

trait ApiResponses
{
    public function apiResponse($message,$status, $data = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $status,
            'data' => $data,
        ], $status);
    }
}
