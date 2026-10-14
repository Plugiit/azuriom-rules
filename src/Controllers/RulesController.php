<?php

namespace Azuriom\Plugin\PlugiitRules\Controllers;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\PlugiitRules\Models\Version;
use Azuriom\Plugin\PlugiitRules\Support\RulesManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RulesController extends Controller
{
    public function __construct(protected RulesManager $rules) {}

    /**
     * Display the current version of the rules.
     */
    public function index(Request $request)
    {
        $current = $this->rules->currentVersion();

        return $this->showVersion($request, $current, true);
    }

    /**
     * Display a previous version of the rules.
     */
    public function show(Request $request, Version $version)
    {
        return $this->showVersion($request, $version, $version->is($this->rules->currentVersion()));
    }

    /**
     * Accept the current version of the rules.
     */
    public function accept(Request $request)
    {
        $validated = $request->validate([
            'version' => ['required', 'integer'],
            'source' => ['nullable', 'in:modal,page'],
            'redirect' => ['nullable', 'string', 'max:2000'],
        ]);

        $current = $this->rules->currentVersion();

        if ($current === null || $current->id !== (int) $validated['version']) {
            return to_route('plugiit-rules.index')
                ->with('error', trans('plugiit-rules::messages.accept.outdated'));
        }

        $this->rules->accept($request->user(), $current, $validated['source'] ?? 'page', $request->ip());

        $redirect = $validated['redirect'] ?? null;
        $isLocal = $redirect !== null && ($redirect === url('/') || Str::startsWith($redirect, url('/').'/'));

        return redirect($isLocal ? $redirect : route('plugiit-rules.index'))
            ->with('success', trans('plugiit-rules::messages.accept.success', ['version' => $current->version]));
    }

    protected function showVersion(Request $request, ?Version $version, bool $isCurrent)
    {
        $user = $request->user();
        $versions = Version::latest('id')->get(['id', 'version', 'summary', 'requires_acceptance', 'sections', 'published_at']);

        // Differences of each version with the one published just before.
        $diffs = $versions->mapWithKeys(fn (Version $v, int $i) => [
            $v->id => $v->diffWith($versions->get($i + 1)),
        ]);

        return view('plugiit-rules::index', [
            'version' => $version,
            'isCurrent' => $isCurrent,
            'versions' => $versions,
            'diffs' => $diffs,
            'mustAccept' => $user !== null && $isCurrent && $version !== null && ! $this->rules->hasAccepted($user),
            'lastAcceptance' => $user !== null ? $this->rules->lastAcceptance($user) : null,
        ]);
    }
}
