<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachHireLocation extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'hero_image',
        'hero_image_alt',
        'hero_image_credit',
        'meta_title',
        'meta_description',
        'intro_heading',
        'intro_content',
        'why_choose_points',
        'faqs',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'why_choose_points' => 'array',
            'faqs' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
