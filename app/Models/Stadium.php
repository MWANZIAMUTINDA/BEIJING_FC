<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'capacity', 'surface', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'capacity'  => 'integer',
        ];
    }

    // Scopes
    public function scopeActive($q)  { return $q->where('is_active', true); }

    // Helpers
    public function getSurfaceLabelAttribute(): string
    {
        return match($this->surface) {
            'grass'      => '🌿 Natural Grass',
            'artificial' => '🟩 Artificial Turf',
            'indoor'     => '🏟️ Indoor',
            default      => ucfirst($this->surface),
        };
    }

    public function getSurfaceBadgeClassAttribute(): string
    {
        return match($this->surface) {
            'grass'      => 'badge-green',
            'artificial' => 'badge-blue',
            'indoor'     => 'badge-orange',
            default      => 'badge-gray',
        };
    }

    /**
     * Matches held at this stadium (matched by venue text).
     */
    public function matches()
    {
        return FootballMatch::where('venue', 'like', '%' . $this->name . '%');
    }
}
