<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_COMPLETED => 'Completed',
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
        'notes',
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
            $reference = 'BT-'.now()->format('Y').'-'.strtoupper(str()->random(5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    public function isRoundTrip(): bool
    {
        return $this->trip_type === 'round_trip';
    }
}
