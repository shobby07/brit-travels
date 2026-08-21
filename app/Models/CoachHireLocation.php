<?php

namespace App\Models;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Collection;

/**
 * A coach-hire location landing page (/coach-hire/{slug}).
 *
 * Static content backed by config/locations.php — see the note on Coach.
 */
class CoachHireLocation implements UrlRoutable
{
    public function __construct(
        public string $slug = '',
        public string $name = '',
        public ?string $hero_image = null,
        public ?string $hero_image_alt = null,
        public ?string $hero_image_credit = null,
        public ?string $intro_heading = null,
        public ?string $intro_content = null,
        public array $why_choose_points = [],
        public array $faqs = [],
        public ?string $meta_title = null,
        public ?string $meta_description = null,
    ) {}

    /** @return Collection<int, static> */
    public static function active(): Collection
    {
        return collect(config('locations', []))->map(fn (array $row) => new static(
            slug: $row['slug'],
            name: $row['name'],
            hero_image: $row['hero_image'] ?? null,
            hero_image_alt: $row['hero_image_alt'] ?? null,
            hero_image_credit: $row['hero_image_credit'] ?? null,
            intro_heading: $row['intro_heading'] ?? null,
            intro_content: $row['intro_content'] ?? null,
            why_choose_points: $row['why_choose_points'] ?? [],
            faqs: $row['faqs'] ?? [],
            meta_title: $row['meta_title'] ?? null,
            meta_description: $row['meta_description'] ?? null,
        ))->values();
    }

    public static function findBySlug(string $slug): ?static
    {
        return static::active()->firstWhere('slug', $slug);
    }

    public function getRouteKey(): string
    {
        return $this->slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        return static::findBySlug((string) $value);
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?static
    {
        return null;
    }
}
