<?php

namespace Azuriom\Plugin\PlugiitRules\Providers;

use Azuriom\Extensions\Plugin\AdminUserEditComposer;
use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Permission;
use Azuriom\Plugin\PlugiitRules\Listeners\RecordRegistrationAcceptance;
use Azuriom\Plugin\PlugiitRules\Middleware\RulesMiddleware;
use Azuriom\Plugin\PlugiitRules\Models\Version;
use Azuriom\Plugin\PlugiitRules\Support\RulesManager;
use Azuriom\Plugin\PlugiitRules\View\Composers\AdminUserAcceptancesComposer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class PlugiitRulesServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     */
    public function register(): void
    {
        $this->app->singleton(RulesManager::class);

        // Appended to the "web" group (and not as a global middleware) so the
        // session, the logged-in user and the current route are available,
        // which is required to block actions before they are executed.
        $this->app->make(Kernel::class)->appendMiddlewareToGroup('web', RulesMiddleware::class);
    }

    /**
     * Bootstrap any plugin services.
     */
    public function boot(): void
    {
        $this->loadViews();

        $this->loadTranslations();

        $this->loadMigrations();

        $this->registerRouteDescriptions();

        $this->registerAdminNavigation();

        Permission::registerPermissions([
            'plugiit-rules.admin' => 'plugiit-rules::admin.permission',
        ]);

        ActionLog::registerLogs([
            'plugiit-rules.versions.published' => [
                'icon' => 'journal-check',
                'color' => 'success',
                'message' => 'plugiit-rules::admin.logs.published',
                'model' => Version::class,
            ],
            'plugiit-rules.settings.updated' => [
                'icon' => 'journal-text',
                'color' => 'info',
                'message' => 'plugiit-rules::admin.logs.settings',
            ],
        ]);

        Event::listen(Registered::class, RecordRegistrationAcceptance::class);

        if (class_exists(AdminUserEditComposer::class)) {
            View::composer('admin.users.edit', AdminUserAcceptancesComposer::class);
        }

        // @plugiitRulesStatus : small widget for themes (acceptance status of the current user).
        Blade::directive('plugiitRulesStatus', fn () => "<?php echo view('plugiit-rules::widget')->render(); ?>");
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            'plugiit-rules.index' => trans('plugiit-rules::messages.title'),
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array<string, array<string, string>>
     */
    protected function adminNavigation(): array
    {
        return [
            'plugiit-rules' => [
                'name' => trans('plugiit-rules::admin.nav.title'),
                'type' => 'dropdown',
                'icon' => 'bi bi-journal-check',
                'route' => 'plugiit-rules.admin.*',
                'permission' => 'plugiit-rules.admin',
                'items' => [
                    'plugiit-rules.admin.index' => [
                        'name' => trans('plugiit-rules::admin.nav.dashboard'),
                        'permission' => 'plugiit-rules.admin',
                    ],
                    'plugiit-rules.admin.sections.index' => [
                        'name' => trans('plugiit-rules::admin.nav.sections'),
                        'permission' => 'plugiit-rules.admin',
                    ],
                    'plugiit-rules.admin.versions.index' => [
                        'name' => trans('plugiit-rules::admin.nav.versions'),
                        'permission' => 'plugiit-rules.admin',
                    ],
                    'plugiit-rules.admin.users.index' => [
                        'name' => trans('plugiit-rules::admin.nav.users'),
                        'permission' => 'plugiit-rules.admin',
                    ],
                    'plugiit-rules.admin.settings' => [
                        'name' => trans('plugiit-rules::admin.nav.settings'),
                        'permission' => 'plugiit-rules.admin',
                    ],
                ],
            ],
        ];
    }
}
