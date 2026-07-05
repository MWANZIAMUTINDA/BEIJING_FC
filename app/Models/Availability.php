<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    protected $fillable = [
        'match_id', 'user_id', 'status', 'is_locked',
        'reason', 'changed_by', 'admin_override',
    ];

    protected function casts(): array
    {
        return [
            'is_locked'      => 'boolean',
            'admin_override' => 'boolean',
        ];
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function match()   { return $this->belongsTo(FootballMatch::class, 'match_id'); }
    public function changer() { return $this->belongsTo(User::class, 'changed_by'); }

    public function isAvailable(): bool  { return $this->status === 'available'; }
    public function isUnavailable(): bool { return $this->status === 'unavailable'; }

    public function getStatusBadge(): array
    {
        $map = [
            'available'   => ['label' => 'Available',   'class' => 'badge-green',  'icon' => '✓'],
            'unavailable' => ['label' => 'Unavailable', 'class' => 'badge-red',    'icon' => '✗'],
            'maybe'       => ['label' => 'Maybe',       'class' => 'badge-yellow', 'icon' => '?'],
        ];

        return $map[$this->status] ?? ['label' => 'Unknown', 'class' => 'badge-gray', 'icon' => '-'];
    }
}
