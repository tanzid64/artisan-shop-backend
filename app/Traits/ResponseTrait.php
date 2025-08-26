<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Standardized JSON response structure
     */
    protected function createResponse(
        mixed $data = null,
        string $message = '',
        int $status = 200,
        bool $success = true,
        ?array $errors = null,
    ): JsonResponse {
        $response = [
            'status' => $status,
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];

        // Only include error fields if they exist
        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Success response with data
     */
    public function responseSuccess(
        mixed $data = null,
        string $message = 'Operation completed successfully',
    ): JsonResponse {
        return $this->createResponse($data, $message, 200, true);
    }

    /**
     * Error response
     */
    public function responseError(
        string $message = 'Operation failed',
        ?array $errors = null,
        int $status = 400
    ): JsonResponse {
        return $this->createResponse(null, $message, $status, false, $errors);
    }

    /**
     * Validation error response
     */
    public function responseValidationError(
        ?array $validationErrors = null,
        string $message = 'Validation failed',
    ): JsonResponse {
        return $this->createResponse(null, $message, 422, false, $validationErrors);
    }

    /**
     * Not found response
     */
    public function responseNotFound(
        string $message = 'Resource not found',
    ): JsonResponse {
        return $this->createResponse(null, $message, 404, false);
    }

    /**
     * Unauthorized response
     */
    public function responseUnauthorized(
        string $message = 'Unauthorized access',
    ): JsonResponse {
        return $this->createResponse(null, $message, 401, false);
    }

    /**
     * Forbidden response
     */
    public function responseForbidden(
        string $message = 'Access forbidden',
    ): JsonResponse {
        return $this->createResponse(null, $message, 403, false);
    }

    /**
     * Created response (for successful resource creation)
     */
    public function responseCreated(
        mixed $data = null,
        string $message = 'Resource created successfully',
    ): JsonResponse {
        return $this->createResponse($data, $message, 201, true);
    }

    /**
     * No content response (for successful deletions/updates)
     */
    public function responseNoContent(
        string $message = 'Operation completed successfully',
    ): JsonResponse {
        return $this->createResponse(null, $message, 204, true);
    }
}
