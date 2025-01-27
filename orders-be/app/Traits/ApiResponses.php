<?php

namespace App\Traits;

trait ApiResponses{

    /**
     * Returns a JSON response with status 200.
     * @param mixed $message
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function ok($message){
        return $this->success($message, 200);
    }

    /**
     * Returns a JSON response with status between 200-299.
     * 
     * @param mixed $message
     * @param int $statusCode
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($message, $statusCode = 200){
        return response()->json([
            'message' => $message, 
            'status' => $statusCode
        ], $statusCode);
    }

    /**
     * Returns a JSON error response
     * 
     * @param mixed $message
     * @param int $statusCode
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($errorMessage, $statusCode = 404){
        return response()->json([
            'error' => $errorMessage, 
            'status' => $statusCode
        ], $statusCode);
    }
}
