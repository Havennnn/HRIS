<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use React\Stream\ThroughStream;

class StreamingJsonResponse implements Responsable
{
    public function __construct(
        protected mixed $data,
        protected array|null $meta = null,
        protected int $status = 200,
        protected array $headers = []
    ) {}

    public function toResponse($request): JsonResponse
    {
        // For now, return a standard JSON response
        // Streaming can be implemented with ReactPHP for large datasets
        $data = ['data' => $this->data];

        if ($this->meta !== null) {
            $data['meta'] = $this->meta;
        }

        return new JsonResponse(
            data: $data,
            status: $this->status,
            headers: $this->headers
        );
    }
}
