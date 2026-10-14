<?php

namespace Azuriom\Plugin\PlugiitRules\Listeners;

use Azuriom\Models\User;
use Azuriom\Plugin\PlugiitRules\Support\RulesManager;
use Azuriom\Plugin\PlugiitRules\Support\RulesSettings;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

/**
 * Record the acceptance of the rules when the checkbox injected in the
 * registration form has been checked.
 */
class RecordRegistrationAcceptance
{
    public function __construct(protected RulesManager $rules, protected Request $request) {}

    public function handle(Registered $event): void
    {
        $version = $this->rules->currentVersion();

        if (! $event->user instanceof User || $version === null
            || ! RulesSettings::requireOnRegister() || ! $this->request->boolean('plugiit_rules_accept')) {
            return;
        }

        $this->rules->accept($event->user, $version, 'register', $this->request->ip());
    }
}
