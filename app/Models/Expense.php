<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category', 'description', 'amount', 'paid_by',
        'expense_date', 'receipt_url', 'match_id',
        'is_approved', 'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'expense_date' => 'date',
            'is_approved'  => 'boolean',
        ];
    }

    public function paidBy()     { return $this->belongsTo(User::class, 'paid_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function match()      { return $this->belongsTo(FootballMatch::class, 'match_id'); }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'turf_hire'    => 'Turf Hire',
            'equipment'    => 'Equipment',
            'refreshments' => 'Refreshments',
            'transport'    => 'Transport',
            default        => 'Miscellaneous',
        };
    }
}
