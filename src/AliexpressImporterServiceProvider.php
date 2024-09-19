<?php

namespace Heptaaurium\AliexpressImporter;

use Heptaaurium\AliexpressImporter\Http\Middleware\TokenFromUrl;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

// Botble Dashboard
use Botble\Base\Facades\DashboardMenu;

class AliexpressImporterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->mergeConfigFrom(
            __DIR__ . '/../config/auth.php',
            'auth'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('token.from.url', TokenFromUrl::class);
        $router->pushMiddlewareToGroup('web', 'token.from.url');

        $this->registerDashboardMenus();

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'aliexpress-importer');
        $this->publishes([
            __DIR__ . '/../config/auth.php' => config_path('auth.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/Migrations/2024_09_16_000000_create_jwt_tokens_table.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_jwt_tokens_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/Http/Middleware/TokenFromUrl.php' => app_path('Http/Middleware/TokenFromUrl.php'),
        ], 'middleware');

        Sanctum::ignoreMigrations();
    }



    protected function registerDashboardMenus(): void
    {
        DashboardMenu::default()->beforeRetrieving(function () {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-core-system',
                    'priority' => 10000,
                    'name' => 'Aliexpress Importer API',
                    'Heptaaurium\AliexpressImporter\Http\Controllers\AuthController@createToken',
                    'icon' => 'ti ti-user-shield',
                    'route' => 'aliexpressimporter.token.create',
                    'permissions' => ['core.system'],
                ]);
        });
    }
}
