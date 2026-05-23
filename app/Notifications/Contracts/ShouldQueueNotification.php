<?php

namespace App\Notifications\Contracts;

interface ShouldQueueNotification
{
    public function queue(string $queue = null): mixed;
}
