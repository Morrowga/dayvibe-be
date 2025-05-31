<?php

namespace App\Traits;

trait CRUDResponses
{
    public function success($message, $data = [])
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function error($message, $data = [])
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
