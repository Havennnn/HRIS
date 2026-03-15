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
     *
     * @return void
     */
    public function handle()
    {
        $this->warn('This will generate API and encryption keys. Be sure that other client-side applications must be using the same generated keys.');

        $confirmed = confirm(
            label: 'Are you sure to proceed with generating keys?',
            default: false,
            yes: 'Proceed',
            no: 'Cancel',
            hint: 'Navigate the options by using arrow keys then press Enter.',
        );

        if (!$confirmed) {
            return;
        }

        $apiKey = Str::random(32);

        $this->comment('Successfully generated! Please use keys below:');
        $this->info("<fg=gray>API Key:</> {$apiKey}");

        $this->saveKeysToEnv($apiKey);
        $this->call('config:cache');
    }

    /**
     * Save generated keys to ENV
     *
     * @param string $apiKey
     *
     * @return void
     */
    protected function saveKeysToEnv(string $apiKey): void
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        $envContent = preg_replace('/^APP_API_KEY=.*$/m', "APP_API_KEY={$apiKey}", $envContent);

        file_put_contents($envContent, $envContent);
    }
}
