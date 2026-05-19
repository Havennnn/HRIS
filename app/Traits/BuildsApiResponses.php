<?php

namespace App\Traits;

use App\Http\Responses\CollectionResponse;
use App\Http\Responses\StreamingJsonResponse;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait BuildsApiResponses
{
    /**
     * Build the appropriate response based on streaming preference.
     *
     * @param  mixed  $data
     */
    protected function buildResponse($data, ?array $metaData = [], ?bool $useStreaming = false): Responsable
    {
        if ($useStreaming) {
            return new StreamingJsonResponse(
                data: $data,
                meta: $metaData ?? null,
                status: Response::HTTP_OK
            );
        }

        return new CollectionResponse(
            data: $data,
            meta: $metaData ?? null,
            status: Response::HTTP_OK
        );
    }

    /**
     * Generate a JSON error response.
     */
    protected function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'error' => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * Generate a JSON unauthorized response.
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Generate a JSON success response.
     *
     * @param  mixed  $data
     */
    protected function successResponse($data, string $message = '', int $code = 200, array $additionalData = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response()->json(array_merge($response, $additionalData), $code);
    }
}
