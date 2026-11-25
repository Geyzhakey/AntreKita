<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeveloperAuditLog extends Model
{
    protected $fillable = [
        'developer_id',
        'action',
        'target_label',
        'details',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public static function record(int $developerId, string $action, string $targetLabel, ?string $details = null): self
    {
        return static::create([
            'developer_id' => $developerId,
            'action' => $action,
            'target_label' => $targetLabel,
            'details' => $details,
        ]);
    }
}
