<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'telegram_user_id',
        'service_id',
        'name',
        'phone',
        'email',
        'budget',
        'requirement',
        'source',
        'status',
        'is_priority',
        'is_contacted',
        'score',
        'score_breakdown',
        'contacted_at',
        'followup_1_sent_at',
        'followup_2_sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'telegram_user_id' => 'integer',
            'is_priority' => 'boolean',
            'is_contacted' => 'boolean',
            'score_breakdown' => 'array',
            'contacted_at' => 'datetime',
            'followup_1_sent_at' => 'datetime',
            'followup_2_sent_at' => 'datetime',
        ];
    }

    // Statuses
    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_CLOSED = 'closed';

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function telegramUser(): BelongsTo
    {
        return $this->belongsTo(TelegramUser::class, 'telegram_user_id', 'telegram_user_id');
    }

    /**
     * Generate a unique lead ID in the format AP-YYYY-XXXXXX.
     */
    public static function generateLeadId(): string
    {
        $prefix = config('telegram.lead_prefix', 'AP');
        $year = now()->year;
        $lastLead = static::where('lead_id', 'like', "{$prefix}-{$year}-%")
            ->orderByDesc('id')
            ->first();

        if ($lastLead) {
            $lastNumber = (int) substr($lastLead->lead_id, -6);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%d-%06d', $prefix, $year, $nextNumber);
    }

    /**
     * Mark the lead as contacted.
     */
    public function markContacted(): self
    {
        $this->update([
            'status' => self::STATUS_CONTACTED,
            'is_contacted' => true,
            'contacted_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark the lead as high priority.
     */
    public function markPriority(): self
    {
        $this->update(['is_priority' => true]);

        return $this;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopePriority($query)
    {
        return $query->where('is_priority', true);
    }

    /**
     * Check if first follow-up is due (24h after creation, not yet sent).
     */
    public function isFirstFollowupDue(): bool
    {
        return ! $this->followup_1_sent_at
            && ! $this->is_contacted
            && $this->created_at->addHours(config('telegram.followup_first', 24))->isPast();
    }

    /**
     * Check if second follow-up is due (72h after creation, not yet sent).
     */
    public function isSecondFollowupDue(): bool
    {
        return $this->followup_1_sent_at
            && ! $this->followup_2_sent_at
            && ! $this->is_contacted
            && $this->created_at->addHours(config('telegram.followup_second', 72))->isPast();
    }
}
