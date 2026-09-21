<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broadcast extends Model
{
    protected $fillable = [
        'name',
        'message',
        'keyboard_buttons',
        'status',
        'total_targets',
        'successful_sends',
        'failed_sends',
        'scheduled_at',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'keyboard_buttons' => 'array',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<BroadcastMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(BroadcastMessage::class);
    }
}
