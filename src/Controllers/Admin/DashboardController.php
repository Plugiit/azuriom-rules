<?php

namespace Azuriom\Plugin\PlugiitRules\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\User;
use Azuriom\Plugin\PlugiitRules\Models\Acceptance;
use Azuriom\Plugin\PlugiitRules\Models\Version;
use Azuriom\Plugin\PlugiitRules\Support\RulesManager;
use Azuriom\Plugin\PlugiitRules\Support\RulesSettings;

class DashboardController extends Controller
{
    public function index(RulesManager $rules)
    {
        $current = $rules->currentVersion();
        $required = $rules->requiredVersion();
        $totalUsers = User::registered()->count();

        $accepted = $required !== null
            ? Acceptance::where('version_id', '>=', $required->id)
                ->whereHas('user', fn ($query) => $query->whereNull('deleted_at'))
                ->distinct()
                ->count('user_id')
            : 0;

        $draftDiff = Version::diffSections($current?->sectionsCollection() ?? collect(), collect($rules->draftSnapshot()));

        return view('plugiit-rules::admin.dashboard', [
            'current' => $current,
            'required' => $required,
            'totalUsers' => $totalUsers,
            'accepted' => $accepted,
            'percent' => $totalUsers > 0 ? round($accepted / $totalUsers * 100, 1) : 0,
            'recent' => Acceptance::with(['user', 'version'])->latest()->take(10)->get(),
            'draftDiff' => $draftDiff,
            'hasDraftChanges' => ! Version::diffIsEmpty($draftDiff),
            'mode' => RulesSettings::mode(),
            'blockedPatterns' => RulesSettings::blockedPatterns(),
        ]);
    }
}
