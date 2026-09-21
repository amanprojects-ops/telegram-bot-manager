<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id',
        'chat_id',
        'state',
        'payload',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'telegram_user_id' => 'integer',
            'chat_id' => 'integer',
            'payload' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    // Conversation states
    public const STATE_IDLE = 'idle';

    public const STATE_QUOTE_SERVICE = 'quote_service';

    public const STATE_QUOTE_BUDGET = 'quote_budget';

    public const STATE_QUOTE_REQUIREMENT = 'quote_requirement';

    public const STATE_QUOTE_NAME = 'quote_name';

    public const STATE_QUOTE_PHONE = 'quote_phone';

    public const STATE_QUOTE_CONFIRM = 'quote_confirm';

    public const STATE_BROADCAST_MESSAGE = 'broadcast_message';

    public const STATE_BROADCAST_AUDIENCE = 'broadcast_audience';

    public const STATE_BROADCAST_CONFIRM = 'broadcast_confirm';

    public const STATE_CYBER_TYPE = 'cyber_type';

    public const STATE_CYBER_TARGET = 'cyber_target';

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_user_id');
    }

    /**
     * Check if the session has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Update the session state and merge payload data.
     *
     * @param  array<string, mixed>  $payloadData
     */
    public function transitionTo(string $state, array $payloadData = []): self
    {
        $this->update([
            'state' => $state,
            'payload' => array_merge($this->payload ?? [], $payloadData),
            'expires_at' => now()->addMinutes(30),
        ]);

        return $this;
    }

    /**
     * Reset the session back to idle.
     */
    public function reset(): self
    {
        $this->update([
            'state' => self::STATE_IDLE,
            'payload' => null,
            'expires_at' => null,
        ]);

        return $this;
    }

    /**
     * Get or create a session for a Telegram user.
     */
    public static function findOrCreateForUser(int $telegramUserId, int $chatId): self
    {
        $session = static::where('telegram_user_id', $telegramUserId)
            ->where('chat_id', $chatId)
            ->first();

        if ($session && $session->isExpired()) {
            $session->reset();
        }

        if (! $session) {
            $session = static::create([
                'telegram_user_id' => $telegramUserId,
                'chat_id' => $chatId,
                'state' => self::STATE_IDLE,
                'expires_at' => now()->addMinutes(30),
            ]);
        }

        return $session;
    }
}
