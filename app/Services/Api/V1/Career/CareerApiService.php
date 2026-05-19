<?php

namespace App\Services\Api\V1\Career;

use App\Http\Resources\Api\V1\Career\CareerResource;
use App\Models\Career;
use App\Models\Position;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareerApiService
{
    use BuildsApiResponses;

    public function list(Request $request): JsonResponse|Responsable
    {
        try {
            $perPage = $request->input('per_page', 15);
            $search = $request->input('search');
            $positionId = $request->input('position_id');

            $query = Career::query()
                ->with('position')
                ->where('is_active', true);

            if ($positionId !== null) {
                $query->where('position_id', $positionId);
            }

            $careers = $query
                ->search($search)
                ->paginate($perPage);

            $collection = CareerResource::collection($careers);

            return $this->buildResponse(
                $collection,
                [
                    'current_page' => $careers->currentPage(),
                    'last_page' => $careers->lastPage(),
                    'per_page' => $careers->perPage(),
                    'total' => $careers->total(),
                    'positions' => Position::options(),
                ]
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve careers: '.$e->getMessage(), 500);
        }
    }

    public function show(Career $career): JsonResponse
    {
        try {
            if (! $career->is_active) {
                return $this->errorResponse('Career not found.', 404);
            }

            $career->load('position');

            return $this->successResponse(
                (new \App\Http\Resources\Api\V1\Career\CareerResource($career))->resolve(request())
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to retrieve career: '.$e->getMessage(), 500);
        }
    }
}
