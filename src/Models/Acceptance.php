<?php

namespace Azuriom\Plugin\PlugiitRules\Models;

use Azuriom\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $version_id
 * @property string|null $ip_hash
 * @property string $source
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Azuriom\Models\User $user
 * @property \Azuriom\Plugin\PlugiitRules\Models\Version $version
 */
class Acceptance extends Model
{
    public const SOURCES = ['modal', 'page', 'register', 'import'];

    protected $table = 'plugiit_rules_acceptances';

    protected $fillable = [
        'user_id', 'version_id', 'ip_hash', 'source',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function version()
    {
        return $this->belongsTo(Version::class);
    }

    /**
     * Short representation of the hashed IP, enough to compare two acceptances.
     */
    public function shortIpHash(): ?string
    {
        return $this->ip_hash !== null ? substr($this->ip_hash, 0, 12) : null;
    }
}
