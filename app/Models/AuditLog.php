<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_id', 'action', 'entity_type', 'entity_id',
        'old_values', 'new_values', 'ip_address', 'user_agent', 'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values'   => 'array',
            'new_values'   => 'array',
            'performed_at' => 'datetime',
        ];
    }

    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }

    public static function record(string $action, ?Model $entity = null, array $old = [], array $new = []): self
    {
        return self::create([
            'actor_id'    => auth()->id(),
            'action'      => $action,
            'entity_type' => $entity ? class_basename($entity) : null,
            'entity_id'   => $entity?->id,
            'old_values'  => $old ?: null,
            'new_values'  => $new ?: null,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }
}
