<?php

namespace Azuriom\Plugin\PlugiitRules\Models;

use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Published, immutable version of the rules.
 *
 * @property int $id
 * @property string $version
 * @property string|null $summary
 * @property bool $requires_acceptance
 * @property array $sections [{key, title, anchor, content}]
 * @property int|null $author_id
 * @property \Carbon\Carbon $published_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Models\User|null $author
 */
class Version extends Model
{
    protected $table = 'plugiit_rules_versions';

    protected $fillable = [
        'version', 'summary', 'requires_acceptance', 'sections', 'author_id', 'published_at',
    ];

    protected $casts = [
        'requires_acceptance' => 'boolean',
        'sections' => 'array',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function acceptances()
    {
        return $this->hasMany(Acceptance::class);
    }

    /**
     * Get the version published just before this one.
     */
    public function previous(): ?self
    {
        return self::where('id', '<', $this->id)->latest('id')->first();
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{key: int|string, title: string, anchor: string, content: string}>
     */
    public function sectionsCollection(): Collection
    {
        return collect($this->sections ?? []);
    }

    /**
     * Summarize the differences with a previous version: titles of the
     * added, removed and modified sections.
     *
     * @return array{added: string[], removed: string[], modified: string[]}
     */
    public function diffWith(?self $previous): array
    {
        return self::diffSections($previous?->sectionsCollection() ?? collect(), $this->sectionsCollection());
    }

    /**
     * @return array{added: string[], removed: string[], modified: string[]}
     */
    public static function diffSections(Collection $old, Collection $new): array
    {
        $old = $old->keyBy('key');
        $new = $new->keyBy('key');

        return [
            'added' => $new->diffKeys($old)->pluck('title')->values()->all(),
            'removed' => $old->diffKeys($new)->pluck('title')->values()->all(),
            'modified' => $new->intersectByKeys($old)
                ->filter(fn (array $section, $key) => $section['title'] !== $old[$key]['title']
                    || self::normalize($section['content']) !== self::normalize($old[$key]['content']))
                ->pluck('title')
                ->values()
                ->all(),
        ];
    }

    public static function diffIsEmpty(array $diff): bool
    {
        return empty($diff['added']) && empty($diff['removed']) && empty($diff['modified']);
    }

    protected static function normalize(string $content): string
    {
        return trim(preg_replace('/\s+/', ' ', $content));
    }
}
