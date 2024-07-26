<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
class MediaHelper
{
    //upload image on s3 bucket
    public function getStorageUrl($image_name) 
    {
        $s3 = Storage::disk('s3')->getAdapter()->getClient();
        return $s3->getObjectUrl( env('AWS_BUCKET'),$image_name);
    }

    //upload image on local system project folder
    public function uploadImage($file) 
    {
       // Handle the file upload
       $extension = $file->getClientOriginalExtension(); // Get file extension
       // Generate a unique name for the file
       $uniqueName = time() . '-' . uniqid() . '.' . $extension;
       // Define the directory path
       $directoryPath = public_path('images');

       // Check if the directory exists, and create it if it does not
       if (!is_dir($directoryPath)) {
           mkdir($directoryPath, 0755, true);
       }
       // Move the file to the 'public/files' directory
       $file->move($directoryPath, $uniqueName);
       // Store file path relative to public path
       return $uniqueName;
    }

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
