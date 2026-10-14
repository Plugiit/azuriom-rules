<?php

namespace Azuriom\Plugin\PlugiitRules\View\Composers;

use Azuriom\Extensions\Plugin\AdminUserEditComposer;
use Azuriom\Models\User;
use Azuriom\Plugin\PlugiitRules\Models\Acceptance;
use Azuriom\Plugin\PlugiitRules\Support\RulesManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminUserAcceptancesComposer extends AdminUserEditComposer
{
    public function __construct(protected RulesManager $rules) {}

    public function getCards(User $user, View $view)
    {
        if (! Gate::allows('plugiit-rules.admin') || $this->rules->currentVersion() === null) {
            return [];
        }

        $view->with('plugiitRulesAcceptances', Acceptance::with('version')->where('user_id', $user->id)->latest('version_id')->get());
        $view->with('plugiitRulesAccepted', $this->rules->hasAccepted($user));
        $view->with('plugiitRulesRequired', $this->rules->requiredVersion());

        return [
            'plugiit-rules' => [
                'name' => trans('plugiit-rules::admin.users.card'),
                'view' => 'plugiit-rules::admin.users.card',
            ],
        ];
    }
}
