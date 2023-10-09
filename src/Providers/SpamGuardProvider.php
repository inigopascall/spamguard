<?php

namespace InigoPascall\SpamGuard\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use InigoPascall\SpamGuard\Exceptions\SpamGuard403Handler;
use InigoPascall\SpamGuard\Models\SpamGuardBlacklist;

class SpamGuardProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $viewPath = resource_path('views/errors/');

        if (!is_dir(dirname($viewPath))) {
            mkdir(dirname($viewPath), 0755, true);
        }

        $this->publishes([
            __DIR__.'/../config/spamguard.php' => config_path('spamguard.php'),
            __DIR__ . '/../resources/views/errors/bannedbyspamguard.blade.php' => resource_path('views/errors/bannedbyspamguard.blade.php'),
            __DIR__ . '/../Exceptions/SpamGuardUnauthorized.php' => app_path('Exceptions/SpamGuardUnauthorized.php'),
        ], 'spamguard-config');

        $this->publishes([
            __DIR__.'/../database/migrations/create_form_submissions_table.php' => database_path('migrations/' . now()->format('Y_m_d_His') . '_create_form_submissions_table.php'),
        ], 'spamguard-submissions');

        $this->publishes([
            __DIR__.'/../database/migrations/create_blacklist_table.php' => database_path('migrations/' . now()->format('Y_m_d_His') . '_create_blacklist_table.php'),
            __DIR__.'/../database/seeders/BlackListTableSeeder.php' => database_path('seeders/BlackListTableSeeder.php'),
        ], 'spamguard-blacklist');


        // use static config or load blacklist from database?
        if(config('spamguard.load_blacklist_from_db'))
        {
            $import = config('spamguard');

            try{
                $import['blacklist'] = SpamGuardBlacklist::all()->pluck('text')->toArray();

                $this->app['config']['spamguard'] = $import;

            }catch (\Exception $exception){

                Log::error('Unable to load configuration files from DB: ' . $exception->getMessage());
            }
        }
    }
}
