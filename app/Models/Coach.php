<?php

namespace App\Models;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * A coach in the fleet.
 *
 * This is static content, not a database record — the fleet lives in
 * config/fleet.php and ships with the app. It implements UrlRoutable so
 * route model binding (/fleet/{coach}) and route() links keep working
 * exactly as they did when this was an Eloquent model.
 */
class Coach implements UrlRoutable
{
    public function __construct(
        public int $id = 0,
        public string $slug = '',
        public string $name = '',
        public int $seats = 0,
        public ?string $description = null,
        public array $amenities = [],
        public ?string $image = null,
        public array $gallery = [],
        public ?string $meta_title = null,
        public ?string $meta_description = null,
    ) {}

    /** @return Collection<int, static> */
    public static function active(): Collection
    {
        return collect(config('fleet', []))->map(fn (array $row) => new static(
            id: $row['id'],
            slug: $row['slug'],
            name: $row['name'],
            seats: $row['seats'],
            description: $row['description'] ?? null,
            amenities: $row['amenities'] ?? [],
            image: $row['image'] ?? null,
            gallery: $row['gallery'] ?? [],
            meta_title: $row['meta_title'] ?? null,
            meta_description: $row['meta_description'] ?? null,
        ))->values();
    }

    public static function findBySlug(string $slug): ?static
    {
        return static::active()->firstWhere('slug', $slug);
    }

    public static function findById(int|string|null $id): ?static
    {
        return $id === null ? null : static::active()->firstWhere('id', (int) $id);
    }

    /** Valid coach ids, for validating the optional coach picker on the forms. */
    public static function ids(): array
    {
        return static::active()->pluck('id')->all();
    }

    /**
     * Public URL for an image path.
     *
     * Fleet photos live under public/images/coaches and are committed with the
     * app, so they resolve straight against the document root and survive a
     * fresh deploy on a host with an ephemeral filesystem.
     */
    public function mediaUrl(string $path): string
    {
        return str_starts_with($path, 'http') ? $path : asset(ltrim($path, '/'));
    }

    /**
     * WebP source for the main image, when one is actually available, so we
     * never advertise a `type="image/webp"` source that isn't a WebP.
     */
    public function imageWebpUrl(): ?string
    {
        if (! $this->image || str_starts_with($this->image, 'http')) {
            return null;
        }

        return str_ends_with($this->image, '.webp') ? $this->mediaUrl($this->image) : null;
    }

    /**
     * URL for the <img> src — the JPEG companion where one exists (the
     * committed photos ship as WebP + JPEG pairs), otherwise the stored file
     * itself so a WebP-only image still renders.
     */
    public function imageDisplayUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http') || ! str_ends_with($this->image, '.webp')) {
            return $this->mediaUrl($this->image);
        }

        $relative = ltrim(Str::replaceLast('.webp', '.jpg', $this->image), '/');

        return is_file(public_path($relative)) ? asset($relative) : $this->mediaUrl($this->image);
    }

    /** Whether this vehicle reads as a minibus rather than a coach. */
    public function vehicleType(): string
    {
        return $this->seats <= 16 ? 'minibus' : 'coach';
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
        return $field === 'id'
            ? static::findById($value)
            : static::findBySlug((string) $value);
    }

    public function resolveChildRouteBinding($childType, $value, $field): ?static
    {
        return null;
    }
}
