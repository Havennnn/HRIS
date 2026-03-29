<?php

namespace App\Console\Commands\Api;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;

class GenerateApiKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:generate-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate application API and encryption keys';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->warn('This will generate API and encryption keys. Be sure that other client-side applications must be using the same generated keys.');

        $confirmed = confirm(
            label: 'Are you sure to proceed with generating keys?',
            default: false,
            yes: 'Proceed',
            no: 'Cancel',
            hint: 'Navigate the options by using arrow keys then press Enter.',
        );

        if (! $confirmed) {
            return;
        }

        $apiKey = Str::random(32);

        $this->saveKeysToEnv($apiKey);

        $this->comment('Successfully generated! Please use keys below:');
        $this->info("<fg=gray>API Key:</> {$apiKey}");

        $this->call('config:clear');
    }

    /**
     * Save generated keys to ENV.
     */
    protected function saveKeysToEnv(string $apiKey): void
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        if ($envContent === false) {
            $this->error('Unable to read the .env file.');
            return;
        }

        $replacement = "APP_API_KEY={$apiKey}";

        if (preg_match('/^APP_API_KEY=.*$/m', $envContent) === 1) {
            $envContent = preg_replace('/^APP_API_KEY=.*$/m', $replacement, $envContent) ?? $envContent;
        } else {
            $envContent = rtrim($envContent) . PHP_EOL . $replacement . PHP_EOL;
        }

        file_put_contents($envFile, $envContent);
    }
}
