<?php

namespace App\Services\Api\V1\Application;

use App\Http\Resources\Api\V1\Application\ApplicationResource;
use App\Models\Application;
use App\Traits\BuildsApiResponses;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use PiaCore\Facades\FileUploader;

class ApplicationApiService
{
    use BuildsApiResponses;

    public function submit(FormRequest $request): JsonResponse
    {
        try {
            $application = DB::transaction(function () use ($request): Application {
                $application = Application::query()->create([
                    'career_id' => $request->validated('career_id'),
                    'first_name' => $request->validated('first_name'),
                    'last_name' => $request->validated('last_name'),
                    'middle_name' => $request->validated('middle_name'),
                    'birthdate' => $request->validated('birthdate'),
                    'mobile_number' => $request->validated('mobile_number'),
                    'email' => $request->validated('email'),
                ]);

                if ($request->hasFile('resume') || $request->hasFile('portfolio')) {
                    $detailData = [];

                    if ($request->hasFile('resume')) {
                        $uploadedFile = FileUploader::upload(
                            $request->file('resume'),
                            $application,
                            ['directory' => 'applications/resumes']
                        );
                        $detailData['resume_id'] = $uploadedFile->id;
                    }

                    if ($request->hasFile('portfolio')) {
                        $uploadedFile = FileUploader::upload(
                            $request->file('portfolio'),
                            $application,
                            ['directory' => 'applications/portfolios']
                        );
                        $detailData['portfolio_id'] = $uploadedFile->id;
                    }

                    $application->details()->create($detailData);
                }

                return $application->fresh('details');
            });

            return $this->successResponse(
                (new ApplicationResource($application))->resolve($request),
                'Application submitted successfully.',
                201
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to submit application: '.$e->getMessage(), 422);
        }
    }
}
