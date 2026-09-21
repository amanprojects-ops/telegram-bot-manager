<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'emoji',
        'price',
        'billing_period',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
        ];
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
     * Get formatted price string.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price) . ' / ' . $this->billing_period;
    }

    /**
     * Get button label with emoji.
     */
    public function getButtonLabelAttribute(): string
    {
        return "{$this->emoji} {$this->name}";
    }
}
