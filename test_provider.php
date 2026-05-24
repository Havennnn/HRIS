<?php
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$provider = $app->getProvider(\PiaCore\Providers\PiaCoreServiceProvider::class);
echo 'Provider found: '.($provider ? 'YES' : 'NO')."\n";

if ($provider) {
    echo 'Notif config routes: '.implode(', ', array_keys(config('notifications.routes', [])))."\n";
    echo 'Has import_failed: '.(config('notifications.routes.import_failed') ? 'YES' : 'NO')."\n";
}
