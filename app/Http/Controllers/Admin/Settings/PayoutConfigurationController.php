<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\PayoutConfigurationRequest;
use App\Models\PayoutConfiguration;
use App\Services\Admin\Settings\PayoutConfigurationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use PiaCore\Http\Resources\ActivityLogResource;
use Spatie\Activitylog\Models\Activity;

class PayoutConfigurationController extends Controller
{
    public function __construct(
        protected PayoutConfigurationService $service
    ) {}

    public function index(Request $request): Response
    {
        $configs = $this->service->getAll();

        $activityLogs = Activity::query()
            ->where('subject_type', PayoutConfiguration::class)
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Settings/PayoutConfigurations/Index', [
            'configs' => $configs,
            'activity_logs' => ActivityLogResource::collection($activityLogs)->response()->getData(true),
        ]);
    }

    public function show(PayoutConfiguration $payoutConfiguration, Request $request): Response
    {
        return Inertia::render('Admin/Settings/PayoutConfigurations/Show', [
            'config' => $payoutConfiguration,
        ]);
    }

    public function update(PayoutConfigurationRequest $request, PayoutConfiguration $payoutConfiguration)
    {
        return $this->service->update($payoutConfiguration, $request->validated());
    }

    public function reset()
    {
        return $this->service->resetToDefaults();
    }
}
