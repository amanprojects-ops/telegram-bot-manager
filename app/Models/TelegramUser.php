<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TelegramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id',
        'username',
        'first_name',
        'last_name',
        'phone',
        'email',
        'language',
        'source',
        'first_seen_at',
        'last_seen_at',
        'is_blocked',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'telegram_user_id' => 'integer',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'is_blocked' => 'boolean',
        ];
    }

    /**
     * Get the user's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }

        return $this->first_name ?? $this->username ?? "User #{$this->telegram_user_id}";
    }

    /**
     * Get or create a Telegram user from an update payload.
     *
     * @param  array<string, mixed>  $from
     */
    public static function findOrCreateFromTelegram(array $from): self
    {
        return static::updateOrCreate(
            ['telegram_user_id' => $from['id']],
            [
                'username' => $from['username'] ?? null,
                'first_name' => $from['first_name'] ?? null,
                'last_name' => $from['last_name'] ?? null,
                'language' => $from['language_code'] ?? 'en',
                'last_seen_at' => now(),
                'first_seen_at' => now(),
            ]
        );
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TelegramSession::class, 'telegram_user_id', 'telegram_user_id');
    }

    public function activeSession(): HasOne
    {
        return $this->hasOne(TelegramSession::class, 'telegram_user_id', 'telegram_user_id')
            ->where('expires_at', '>', now())
            ->latest('updated_at');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(TelegramLead::class, 'telegram_user_id', 'telegram_user_id');
    }

    public function brochureDownloads(): HasMany
    {
        return $this->hasMany(BrochureDownload::class, 'telegram_user_id', 'telegram_user_id');
    }

    /**
     * Scope: non-blocked users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeActive($query)
    {
        return $query->where('is_blocked', false);
    }

    /**
     * Scope: users seen in the last N days.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeRecentlyActive($query, int $days = 30)
    {
        return $query->where('last_seen_at', '>=', now()->subDays($days));
    }
}
