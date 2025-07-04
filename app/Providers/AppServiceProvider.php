<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Translation\SettingTranslation;
use Config;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Facades\Health;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\BackupsCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DatabaseSizeCheck;
use Spatie\Health\Checks\Checks\DatabaseTableSizeCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\PingCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        Health::checks([
            UsedDiskSpaceCheck::new(),
            CacheCheck::new(),
            BackupsCheck::new()
                ->locatedAt('/path/to/backups/*.zip')
                ->youngestBackShouldHaveBeenMadeBefore(now()->subDays(1)),
            OptimizedAppCheck::new(),
            DatabaseCheck::new(),
            DatabaseConnectionCountCheck::new()
                ->warnWhenMoreConnectionsThan(50)
                ->failWhenMoreConnectionsThan(100),
            DatabaseSizeCheck::new()
                ->failWhenSizeAboveGb(errorThresholdGb: 5.0),
            DatabaseTableSizeCheck::new()
                ->table('webinars', maxSizeInMb: 1_000)
                ->table('jobs', maxSizeInMb: 2_000),
            DebugModeCheck::new(),
            EnvironmentCheck::new()->expectEnvironment('production'),
            PingCheck::new()->url(config('app.url'))->name('Home Page')->timeout(5),
            QueueCheck::new(),
            ScheduleCheck::new(),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::defaultView('pagination::default');

        //Get all settings from the database and set them in the config under the settings key
        $app_setting = Setting::where('name', Setting::$appSettings)->first();
        if ($app_setting) {
            $settings = SettingTranslation::where('setting_id', $app_setting->id)
                ->get()
                ->pluck('value')
                ->toArray();

            foreach ($settings as $key => $value) {
                $decoded = json_decode($value);
                foreach ($decoded as $settingKey => $settingData) {
                    Config::set("settings.{$settingKey}", $settingData->value);
                }
            }
        }
    }
}
