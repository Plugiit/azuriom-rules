<?php

namespace Azuriom\Plugin\PlugiitRules\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Draft section of the rules: published as a snapshot in a Version.
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $position
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Section extends Model
{
    protected $table = 'plugiit_rules_sections';

    protected $fillable = [
        'title', 'content', 'position',
    ];
}
