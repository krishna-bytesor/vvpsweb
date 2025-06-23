<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function respondWith($result, $message = "Action successful.", $code = 200, $redirect = null)
    {
        $response = [
            'data' => $result,
            'message' => $message ?? 'SUCCESS',
            'success' => true,
            'errors' => []
        ];
        if (!empty($redirect)) {
            $response['redirect'] = $redirect;
        }
        return response()->json($response, $code ?? 200);
    }

    public function failedResponse($result, $message = "Action unsuccessful.", $code = 400)
    {
        $response = [
            'data' => $result,
            'message' => $message ?? 'FAILURE',
            'success' => false,
            'errors' => []
        ];

        return response()->json($response, $code);
    }
}
