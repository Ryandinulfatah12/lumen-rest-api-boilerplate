<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class Helper
{
    public static function jsonResponse($data, $status = 200, $headers= [])
    {
        return new JsonResponse($data, $status, $headers);
    }
}