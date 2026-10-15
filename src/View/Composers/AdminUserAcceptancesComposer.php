<?php

namespace Azuriom\Plugin\Rules\View\Composers;

use Azuriom\Extensions\Plugin\AdminUserEditComposer;
use Azuriom\Models\User;
use Azuriom\Plugin\Rules\Models\Acceptance;
use Azuriom\Plugin\Rules\Support\RulesManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminUserAcceptancesComposer extends AdminUserEditComposer
{
    public function __construct(protected RulesManager $rules) {}

    public function getCards(User $user, View $view)
    {
        if (! Gate::allows('rules.admin') || $this->rules->currentVersion() === null) {
            return [];
        }

        $view->with('plugiitRulesAcceptances', Acceptance::with('version')->where('user_id', $user->id)->latest('version_id')->get());
        $view->with('plugiitRulesAccepted', $this->rules->hasAccepted($user));
        $view->with('plugiitRulesRequired', $this->rules->requiredVersion());

        return [
            'rules' => [
                'name' => trans('rules::admin.users.card'),
                'view' => 'rules::admin.users.card',
            ],
        ];
    }
}
