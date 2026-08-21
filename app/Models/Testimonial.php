<?php

namespace App\Models;

use Illuminate\Support\Collection;

/** A customer testimonial. Static content backed by config/testimonials.php. */
class Testimonial
{
    public function __construct(
        public string $author = '',
        public ?string $role = null,
        public string $quote = '',
        public int $rating = 5,
    ) {}

    /** @return Collection<int, static> */
    public static function active(): Collection
    {
        return collect(config('testimonials', []))->map(fn (array $row) => new static(
            author: $row['author'],
            role: $row['role'] ?? null,
            quote: $row['quote'],
            rating: $row['rating'] ?? 5,
        ))->values();
    }
}
