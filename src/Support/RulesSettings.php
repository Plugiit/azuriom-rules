<?php

namespace Azuriom\Plugin\Rules\Support;

class RulesSettings
{
    /**
     * Display mode for users who must accept the rules: "modal" (blocking),
     * "banner" (non-blocking) or "none".
     */
    public static function mode(): string
    {
        $mode = setting('rules.mode', 'modal');

        return in_array($mode, RulesManager::MODES, true) ? $mode : 'modal';
    }

    public static function blockActions(): bool
    {
        return (bool) setting('rules.block', false);
    }

    /**
     * @return string[]
     */
    public static function blockedGroups(): array
    {
        return array_values(array_intersect(self::json('rules.block_groups'), array_keys(RulesManager::ACTION_GROUPS)));
    }

    /**
     * @return string[]
     */
    public static function customPatterns(): array
    {
        return array_values(array_filter(self::json('rules.block_routes')));
    }

    /**
     * Route name patterns blocked until the rules are accepted.
     *
     * @return string[]
     */
    public static function blockedPatterns(): array
    {
        if (! self::blockActions()) {
            return [];
        }

        $patterns = collect(self::blockedGroups())
            ->flatMap(fn (string $group) => RulesManager::ACTION_GROUPS[$group]);

        return $patterns->merge(self::customPatterns())->unique()->values()->all();
    }

    public static function requireOnRegister(): bool
    {
        return (bool) setting('rules.register', false);
    }

    protected static function json(string $key): array
    {
        $value = setting($key);

        if (is_array($value)) {
            return $value;
        }

        return is_string($value) ? (json_decode($value, true) ?? []) : [];
    }
}
