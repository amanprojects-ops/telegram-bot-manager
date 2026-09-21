<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'update_id',
        'telegram_user_id',
        'event_type',
        'payload',
        'response',
        'status',
        'error',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'update_id' => 'integer',
            'telegram_user_id' => 'integer',
            'payload' => 'array',
            'response' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Event types
    public const EVENT_MESSAGE = 'message';

    public const EVENT_CALLBACK = 'callback_query';

    public const EVENT_COMMAND = 'command';

    public const EVENT_WEBHOOK = 'webhook';

    // Statuses
    public const STATUS_RECEIVED = 'received';

    public const STATUS_PROCESSED = 'processed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_DUPLICATE = 'duplicate';

    /**
     * Check if an update has already been processed.
     */
    public static function isDuplicate(int $updateId): bool
    {
        return static::where('update_id', $updateId)->exists();
    }

    /**
     * Log a webhook update.
     *
     * @param  array<string, mixed>  $payload
     */
    public static function logUpdate(
        int $updateId,
        ?int $telegramUserId,
        string $eventType,
        array $payload,
        string $status = self::STATUS_RECEIVED,
    ): self {
        return static::create([
            'update_id' => $updateId,
            'telegram_user_id' => $telegramUserId,
            'event_type' => $eventType,
            'payload' => $payload,
            'status' => $status,
            'created_at' => now(),
        ]);
    }
}
