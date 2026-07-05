<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username', 'name', 'email', 'phone', 'position', 'role',
        'is_active', 'avatar', 'password', 'emergency_contact',
        'emergency_phone', 'jersey_number', 'date_joined', 'billing_type',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
            'date_joined'       => 'date',
        ];
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────
    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isTreasurer(): bool { return $this->role === 'treasurer'; }
    public function isCoach(): bool     { return $this->role === 'coach'; }
    public function isMember(): bool    { return $this->role === 'member'; }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }

    // ─── Role Display ─────────────────────────────────────────────────────────
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'     => 'Admin',
            'treasurer' => 'Treasurer',
            'coach'     => 'Coach',
            'member'    => 'Member',
            default     => ucfirst($this->role),
        };
    }

    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'admin'     => 'gold',
            'treasurer' => 'blue',
            'coach'     => 'emerald',
            'member'    => 'secondary',
            default     => 'secondary',
        };
    }

    // ─── Position Label ───────────────────────────────────────────────────────
    public function positionLabel(): string
    {
        return match($this->position) {
            'GK' => 'Goalkeeper',
            'DF' => 'Defender',
            'MF' => 'Midfielder',
            'FW' => 'Forward',
            default => $this->position ?? '—',
        };
    }

    // ─── Avatar ───────────────────────────────────────────────────────────────
    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=10B981&color=fff&size=80';
    }

    // ─── Relationships ────────────────────────────────────────────────────────
    public function payments()       { return $this->hasMany(Payment::class); }
    public function availabilities() { return $this->hasMany(Availability::class); }
    public function balance()        { return $this->hasOne(MemberBalance::class, 'user_id'); }
    public function expenses()       { return $this->hasMany(Expense::class, 'paid_by'); }
    public function auditLogs()      { return $this->hasMany(AuditLog::class, 'actor_id'); }

    public function availabilityFor(int $matchId): Availability
    {
        return $this->availabilities()->firstOrCreate(
            ['match_id' => $matchId],
            [
                'status' => 'unavailable',
                'is_locked' => false,
            ]
        );
    }
}
