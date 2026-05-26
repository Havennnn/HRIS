<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RequestModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_has_activity_logs_trait(): void
    {
        $this->assertContains(
            'PiaCore\Models\Concerns\HasActivityLogs',
            class_uses_recursive(Request::class)
        );
    }

    public function test_request_has_archives_trait(): void
    {
        $this->assertContains(
            'PiaCore\Models\Concerns\HasArchives',
            class_uses_recursive(Request::class)
        );
    }
}
