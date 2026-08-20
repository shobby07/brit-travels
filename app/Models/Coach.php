<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Coach extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'seats',
        'description',
        'amenities',
        'image',
        'gallery',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'gallery' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Public URL for a stored image path.
     *
     * Seeded fleet photos live under public/images/coaches and are committed
     * with the app, so — like the location heroes — they resolve straight
     * against the document root and survive a fresh deploy on a host with an
     * ephemeral filesystem. Admin uploads go to the configured filesystem disk
     * (the local public disk in dev, object storage in production) and are
     * resolved through Storage instead.
     */
    public function mediaUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return str_starts_with(ltrim($path, '/'), 'images/')
            ? asset(ltrim($path, '/'))
            : Storage::url($path);
    }

    /**
     * WebP source for the main image, when one is actually available.
     * Returns null for uploads saved in another format so we never advertise
     * a `type="image/webp"` source that isn't a WebP.
     */
    public function imageWebpUrl(): ?string
    {
        if (! $this->image || str_starts_with($this->image, 'http')) {
            return null;
        }

        return str_ends_with($this->image, '.webp') ? $this->mediaUrl($this->image) : null;
    }

    /**
     * URL for the <img> src — the JPEG companion where we can confirm one
     * exists (the committed photos ship as WebP + JPEG pairs), otherwise the
     * stored file itself so a WebP-only upload still renders.
     */
    public function imageDisplayUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http') || ! str_ends_with($this->image, '.webp')) {
            return $this->mediaUrl($this->image);
        }

        $jpg = Str::replaceLast('.webp', '.jpg', $this->image);
        $relative = ltrim($jpg, '/');

        if (str_starts_with($relative, 'images/')) {
            return is_file(public_path($relative)) ? asset($relative) : $this->mediaUrl($this->image);
        }

        return Storage::exists($jpg) ? Storage::url($jpg) : $this->mediaUrl($this->image);
    }

    /**
     * Whether this coach's vehicle reads as a minibus rather than a coach,
     * used for alt text and copy.
     */
    public function vehicleType(): string
    {
        return $this->seats <= 16 ? 'minibus' : 'coach';
    }
}
