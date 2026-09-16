<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'method',
        'path',
        'status_code',
        'duration_ms',
        'memory_usage_bytes',
        'peak_memory_usage_bytes',
        'is_slow',
        'is_error',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'duration_ms' => 'float',
            'memory_usage_bytes' => 'integer',
            'peak_memory_usage_bytes' => 'integer',
            'is_slow' => 'boolean',
            'is_error' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}