<?php

namespace Azuriom\Plugin\Rules\Support;

use Azuriom\Models\User;
use Azuriom\Plugin\Rules\Models\Acceptance;
use Azuriom\Plugin\Rules\Models\Section;
use Azuriom\Plugin\Rules\Models\Version;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RulesManager
{
    public const MODES = ['modal', 'banner', 'none'];

    /**
     * Groups of actions that can be blocked until the rules are accepted,
     * as route name patterns (only non-GET requests are blocked).
     */
    public const ACTION_GROUPS = [
        'forum' => ['forum.*'],
        'shop' => ['shop.*'],
        'comments' => ['posts.comments.*'],
        'support' => ['support.*'],
        'vote' => ['vote.*'],
    ];

    protected const CACHE_KEY = 'rules.versions';

    /**
     * Versions are cached in memory for the current request too.
     */
    protected ?array $versions = null;

    public function currentVersion(): ?Version
    {
        return $this->versions()['current'];
    }

    /**
     * Get the latest version that requires an acceptance.
     */
    public function requiredVersion(): ?Version
    {
        return $this->versions()['required'];
    }

    public function hasAccepted(User $user): bool
    {
        $required = $this->requiredVersion();

        if ($required === null) {
            return true;
        }

        return Cache::remember($this->userCacheKey($user, $required), now()->addDay(), function () use ($user, $required) {
            return Acceptance::where('user_id', $user->id)
                ->where('version_id', '>=', $required->id)
                ->exists();
        });
    }

    /**
     * Get the latest acceptance of the user, if any.
     */
    public function lastAcceptance(User $user): ?Acceptance
    {
        return Acceptance::with('version')->where('user_id', $user->id)->latest('version_id')->first();
    }

    public function accept(User $user, Version $version, string $source, ?string $ip): Acceptance
    {
        $acceptance = Acceptance::firstOrCreate([
            'user_id' => $user->id,
            'version_id' => $version->id,
        ], [
            'ip_hash' => $this->hashIp($ip),
            'source' => $source,
        ]);

        if ($required = $this->requiredVersion()) {
            Cache::forget($this->userCacheKey($user, $required));
        }

        return $acceptance;
    }

    /**
     * Publish the current draft sections as a new version.
     */
    public function publish(string $number, ?string $summary, bool $requiresAcceptance, ?User $author): Version
    {
        $version = DB::transaction(fn () => Version::create([
            'version' => $number,
            'summary' => $summary,
            'requires_acceptance' => $requiresAcceptance,
            'sections' => $this->draftSnapshot(),
            'author_id' => $author?->id,
            'published_at' => now(),
        ]));

        $this->flush();

        return $version;
    }

    /**
     * Snapshot of the draft sections, in the format stored in versions.
     *
     * @return array<int, array{key: int, title: string, anchor: string, content: string}>
     */
    public function draftSnapshot(): array
    {
        $anchors = [];

        return Section::orderBy('position')->get()->map(function (Section $section, int $index) use (&$anchors) {
            $anchor = Str::slug($section->title) ?: 'section-'.($index + 1);

            while (in_array($anchor, $anchors, true)) {
                $anchor .= '-'.($index + 1);
            }

            $anchors[] = $anchor;

            return [
                'key' => $section->id,
                'title' => $section->title,
                'anchor' => $anchor,
                'content' => $section->content,
            ];
        })->all();
    }

    /**
     * Suggest the next version number from the current one ("1.0" -> "2.0").
     */
    public function suggestNextNumber(): string
    {
        $current = $this->currentVersion()?->version;

        if ($current === null) {
            return '1.0';
        }

        if (preg_match('/^v?(\d+)(?:\.\d+)*$/i', $current, $matches)) {
            return ((int) $matches[1] + 1).'.0';
        }

        return $current.'-2';
    }

    public function hashIp(?string $ip): ?string
    {
        return $ip !== null ? hash_hmac('sha256', $ip, (string) config('app.key')) : null;
    }

    public function flush(): void
    {
        $this->versions = null;

        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @return array{current: Version|null, required: Version|null}
     */
    protected function versions(): array
    {
        return $this->versions ??= Cache::remember(self::CACHE_KEY, now()->addDay(), fn () => [
            'current' => Version::latest('id')->first(),
            'required' => Version::where('requires_acceptance', true)->latest('id')->first(),
        ]);
    }

    protected function userCacheKey(User $user, Version $required): string
    {
        return "rules.accepted.{$user->id}.{$required->id}";
    }
}
