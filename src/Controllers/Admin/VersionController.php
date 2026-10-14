<?php

namespace Azuriom\Plugin\PlugiitRules\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Plugin\PlugiitRules\Models\Section;
use Azuriom\Plugin\PlugiitRules\Models\Version;
use Azuriom\Plugin\PlugiitRules\Support\RulesManager;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    public function __construct(protected RulesManager $rules) {}

    public function index()
    {
        $versions = Version::with('author')->withCount('acceptances')->latest('id')->get();

        return view('plugiit-rules::admin.versions.index', [
            'versions' => $versions,
            'diffs' => $versions->mapWithKeys(fn (Version $v, int $i) => [$v->id => $v->diffWith($versions->get($i + 1))]),
        ]);
    }

    /**
     * Show the form to publish the draft as a new version.
     */
    public function create()
    {
        $current = $this->rules->currentVersion();

        return view('plugiit-rules::admin.versions.create', [
            'current' => $current,
            'suggested' => $this->rules->suggestNextNumber(),
            'sectionsCount' => Section::count(),
            'diff' => Version::diffSections($current?->sectionsCollection() ?? collect(), collect($this->rules->draftSnapshot())),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => ['required', 'string', 'max:20', 'unique:plugiit_rules_versions,version'],
            'summary' => ['nullable', 'string', 'max:2000'],
        ], [], [
            'version' => trans('plugiit-rules::admin.versions.fields.version'),
            'summary' => trans('plugiit-rules::admin.versions.fields.summary'),
        ]);

        if (Section::count() === 0) {
            return to_route('plugiit-rules.admin.sections.index')
                ->with('error', trans('plugiit-rules::admin.versions.no_sections'));
        }

        $version = $this->rules->publish(
            $validated['version'],
            $validated['summary'] ?? null,
            $request->boolean('requires_acceptance'),
            $request->user(),
        );

        ActionLog::log('plugiit-rules.versions.published', $version, ['version' => $version->version]);

        return to_route('plugiit-rules.admin.versions.show', $version)
            ->with('success', trans('plugiit-rules::admin.versions.published', ['version' => $version->version]));
    }

    public function show(Version $version)
    {
        $acceptances = $version->acceptances()->with('user')->latest()->paginate(25);

        return view('plugiit-rules::admin.versions.show', [
            'version' => $version->load('author'),
            'diff' => $version->diffWith($version->previous()),
            'acceptances' => $acceptances,
            'isCurrent' => $version->is($this->rules->currentVersion()),
        ]);
    }
}
