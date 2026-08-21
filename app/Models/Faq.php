<?php

namespace App\Models;

use Illuminate\Support\Collection;

/** A site-wide FAQ entry. Static content backed by config/faqs.php. */
class Faq
{
    public function __construct(
        public string $question = '',
        public string $answer = '',
    ) {}

    /** @return Collection<int, static> */
    public static function active(): Collection
    {
        return collect(config('faqs', []))
            ->map(fn (array $row) => new static($row['question'], $row['answer']))
            ->values();
    }
}
