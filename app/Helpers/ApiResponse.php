<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
class ApiResponse
{
    /**
     * Send a success response.
     *
     * @param mixed $result
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSuccessResponse($result = null, $message = 'Operation successful', $code = 200): JsonResponse
    {
        $response = [
            'message' => $message,
            'data'    => $result,
            'success' => true,
        ];

        return response()->json($response, $code);
    }

     /**
     * Send an error response.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendErrorResponse($error, $errorMessages = [], $code = 404): JsonResponse
    {
        $response = [
            'message' => $error,
            'success' => false,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
