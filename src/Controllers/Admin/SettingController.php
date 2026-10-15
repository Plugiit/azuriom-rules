<?php

namespace Azuriom\Plugin\Rules\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Rules\Support\RulesManager;
use Azuriom\Plugin\Rules\Support\RulesSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function show()
    {
        return view('rules::admin.settings', [
            'mode' => RulesSettings::mode(),
            'block' => RulesSettings::blockActions(),
            'groups' => RulesSettings::blockedGroups(),
            'customPatterns' => RulesSettings::customPatterns(),
            'register' => RulesSettings::requireOnRegister(),
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'mode' => ['required', Rule::in(RulesManager::MODES)],
            'groups' => ['nullable', 'array'],
            'groups.*' => [Rule::in(array_keys(RulesManager::ACTION_GROUPS))],
            'routes' => ['nullable', 'string', 'max:2000'],
        ]);

        $routes = collect(preg_split('/[\s,]+/', (string) ($validated['routes'] ?? '')))
            ->map(fn (string $route) => trim($route))
            ->filter(fn (string $route) => preg_match('/^[A-Za-z0-9_.\-*]+$/', $route))
            ->unique()
            ->values()
            ->all();

        Setting::updateSettings([
            'rules.mode' => $validated['mode'],
            'rules.block' => $request->boolean('block'),
            'rules.block_groups' => json_encode($validated['groups'] ?? []),
            'rules.block_routes' => json_encode($routes),
            'rules.register' => $request->boolean('register'),
        ]);

        ActionLog::log('rules.settings.updated');

        return to_route('rules.admin.settings')
            ->with('success', trans('messages.status.success'));
    }
}
