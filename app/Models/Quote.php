<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_RESPONDED = 'responded';
    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_RESPONDED => 'Responded',
        self::STATUS_CLOSED => 'Closed',
    ];

    protected $fillable = [
        'reference',
        'trip_type',
        'pickup_location',
        'dropoff_location',
        'pickup_date',
        'pickup_time',
        'return_date',
        'return_time',
        'passengers',
        'coach_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'pickup_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'QT-'.now()->format('Y').'-'.strtoupper(str()->random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function isRoundTrip(): bool
    {
        return $this->trip_type === 'round_trip';
    }
}
