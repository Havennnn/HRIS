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
     * @param mixed $data
     * @param array|null $metaData
     * @param bool|null $useStreaming
     *
     * @return \Illuminate\Contracts\Support\Responsable
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
     *
     * @param string $message
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message, int $status): JsonResponse
    {
        return response()->json([
            'error'  => $message,
            'status' => $status,
        ], $status);
    }

    /**
     * Generate a JSON unauthorized response.
     *
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Generate a JSON success response.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $additionalData
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, string $message = '', int $code = 200, array $additionalData = []): JsonResponse
    {
        $response = [
            'success' => true,
            'data'    => $data,
        ];

        if (! empty($message)) {
            $response['message'] = $message;
        }

        return response()->json(array_merge($response, $additionalData), $code);
    }
}
