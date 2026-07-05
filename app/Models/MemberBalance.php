<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberBalance extends Model
{
    protected $table = 'member_balances';

    protected $fillable = [
        'user_id', 'total_paid', 'total_owed', 'balance',
        'matches_paid', 'months_paid', 'last_payment_at',
    ];

    protected function casts(): array
    {
        return [
            'total_paid'      => 'decimal:2',
            'total_owed'      => 'decimal:2',
            'balance'         => 'decimal:2',
            'last_payment_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }

    public function isInCredit(): bool { return $this->balance >= 0; }
    public function isInDebt(): bool   { return $this->balance < 0; }

    public function getStatusLabel(): string
    {
        if ($this->balance >= 0) {
            return 'Paid';
        }
        
        $hasPending = \App\Models\Payment::where('user_id', $this->user_id)
            ->where('status', 'pending')
            ->exists();
            
        if ($hasPending) {
            return 'Pending';
        }

        if ($this->total_paid > 0) {
            return 'Partial';
        }

        return 'Outstanding';
    }

    public function getStatusClass(): string
    {
        return match($this->getStatusLabel()) {
            'Paid'        => 'badge-green',
            'Pending'     => 'badge-yellow',
            'Partial'     => 'badge-orange',
            'Outstanding' => 'badge-red',
            default       => 'badge-gray',
        };
    }

    /**
     * Recalculates total paid, total owed, and net balance for a given user.
     */
    public static function recalculate(int $userId): self
    {
        $user = User::findOrFail($userId);
        $totalPaid = Payment::where('user_id', $userId)->where('status', 'confirmed')->sum('amount');
        $totalOwed = 0;

        if ($user->billing_type === 'monthly') {
            // Monthly calculation: KSh 2080 per month since they joined
            $start = $user->date_joined ?? $user->created_at ?? now();
            $end = now();
            $diffMonths = (($end->format('Y') - $start->format('Y')) * 12) + ($end->format('m') - $start->format('m')) + 1;
            $diffMonths = max(1, $diffMonths);
            $totalOwed = $diffMonths * 2080;
        } else {
            // Pay Per Match calculation: KSh 350 for every completed match they were marked available
            $matches = FootballMatch::where('status', 'completed')
                ->whereHas('availabilities', function ($q) use ($userId) {
                    $q->where('user_id', $userId)->where('status', 'available');
                })->get();

            foreach ($matches as $match) {
                $totalOwed += $match->match_fee > 0 ? $match->match_fee : 350;
            }
        }

        $balance = $totalPaid - $totalOwed;

        // Calculate count of matches paid or months paid as estimate
        $matchesPaid = 0;
        $monthsPaid = 0;
        if ($user->billing_type === 'monthly') {
            $monthsPaid = floor($totalPaid / 2080);
        } else {
            $matchesPaid = floor($totalPaid / 350);
        }

        $lastPayment = Payment::where('user_id', $userId)->where('status', 'confirmed')->latest()->first();

        return self::updateOrCreate(
            ['user_id' => $userId],
            [
                'total_paid'      => $totalPaid,
                'total_owed'      => $totalOwed,
                'balance'         => $balance,
                'matches_paid'    => $matchesPaid,
                'months_paid'     => $monthsPaid,
                'last_payment_at' => $lastPayment ? $lastPayment->created_at : null,
            ]
        );
    }
}
