<?php

namespace Azuriom\Plugin\Rules\Providers;

use Azuriom\Extensions\Plugin\AdminUserEditComposer;
use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Permission;
use Azuriom\Plugin\Rules\Listeners\RecordRegistrationAcceptance;
use Azuriom\Plugin\Rules\Middleware\RulesMiddleware;
use Azuriom\Plugin\Rules\Models\Version;
use Azuriom\Plugin\Rules\Support\RulesManager;
use Azuriom\Plugin\Rules\View\Composers\AdminUserAcceptancesComposer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class RulesServiceProvider extends BasePluginServiceProvider
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
            'rules.admin' => 'rules::admin.permission',
        ]);

        ActionLog::registerLogs([
            'rules.versions.published' => [
                'icon' => 'journal-check',
                'color' => 'success',
                'message' => 'rules::admin.logs.published',
                'model' => Version::class,
            ],
            'rules.settings.updated' => [
                'icon' => 'journal-text',
                'color' => 'info',
                'message' => 'rules::admin.logs.settings',
            ],
        ]);

        Event::listen(Registered::class, RecordRegistrationAcceptance::class);

        if (class_exists(AdminUserEditComposer::class)) {
            View::composer('admin.users.edit', AdminUserAcceptancesComposer::class);
        }

        // @plugiitRulesStatus : small widget for themes (acceptance status of the current user).
        Blade::directive('plugiitRulesStatus', fn () => "<?php echo view('rules::widget')->render(); ?>");
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array<string, string>
     */
    protected function routeDescriptions(): array
    {
        return [
            'rules.index' => trans('rules::messages.title'),
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
            'rules' => [
                'name' => trans('rules::admin.nav.title'),
                'type' => 'dropdown',
                'icon' => 'bi bi-journal-check',
                'route' => 'rules.admin.*',
                'permission' => 'rules.admin',
                'items' => [
                    'rules.admin.index' => [
                        'name' => trans('rules::admin.nav.dashboard'),
                        'permission' => 'rules.admin',
                    ],
                    'rules.admin.sections.index' => [
                        'name' => trans('rules::admin.nav.sections'),
                        'permission' => 'rules.admin',
                    ],
                    'rules.admin.versions.index' => [
                        'name' => trans('rules::admin.nav.versions'),
                        'permission' => 'rules.admin',
                    ],
                    'rules.admin.users.index' => [
                        'name' => trans('rules::admin.nav.users'),
                        'permission' => 'rules.admin',
                    ],
                    'rules.admin.settings' => [
                        'name' => trans('rules::admin.nav.settings'),
                        'permission' => 'rules.admin',
                    ],
                ],
            ],
        ];
    }
}
