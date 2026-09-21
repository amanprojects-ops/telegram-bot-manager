<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'emoji',
        'short_description',
        'description',
        'technologies',
        'suitable_for',
        'website_url',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'suitable_for' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function brochures(): HasMany
    {
        return $this->hasMany(Brochure::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(TelegramLead::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the formatted label with emoji for inline buttons.
     */
    public function getButtonLabelAttribute(): string
    {
        return "{$this->emoji} {$this->name}";
    }
}
