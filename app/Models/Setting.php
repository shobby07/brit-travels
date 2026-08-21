<?php

namespace App\Models;

/**
 * Site settings.
 *
 * Backed by config/site.php rather than a database table — the API is kept as
 * Setting::get() so the views and mailers that call it (and the setting()
 * helper) did not need to change.
 */
class Setting
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = config("site.{$key}", $default);

        // Treat a blank setting the same as a missing one, so callers relying
        // on the default (e.g. an unset social link) behave as before.
        return ($value === null || $value === '') ? $default : (string) $value;
    }
}
