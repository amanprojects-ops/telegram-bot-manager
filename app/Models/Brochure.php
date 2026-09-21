<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brochure extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'title',
        'file_path',
        'telegram_file_id',
        'version',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(BrochureDownload::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full storage path.
     */
    public function getFullPathAttribute(): string
    {
        return storage_path("app/public/{$this->file_path}");
    }

    /**
     * Check if a Telegram file_id is cached for this brochure.
     */
    public function hasTelegramFileId(): bool
    {
        return ! empty($this->telegram_file_id);
    }
}
