<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ExpenseApiController extends Controller
{
    /**
     * Get expenses list and financial breakdown summary.
     */
    public function index()
    {
        $expenses = Expense::with(['paidBy', 'match'])
            ->latest()
            ->paginate(20);

        $totalExpenses      = Expense::where('is_approved', true)->sum('amount');
        $totalContributions = Payment::where('status', 'confirmed')->sum('amount');
        $netBalance         = $totalContributions - $totalExpenses;

        $byCategory = Expense::where('is_approved', true)
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_expenses' => (float)$totalExpenses,
                'total_contributions' => (float)$totalContributions,
                'net_balance' => (float)$netBalance,
                'by_category' => $byCategory,
                'expenses' => $expenses
            ]
        ]);
    }

    /**
     * Record a new club expense (admin or treasurer only).
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'treasurer'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Only admins and treasurers can record expenses.'
            ], 403);
        }

        $data = $request->validate([
            'category'     => 'required|in:turf,equipment,refreshments,transport,medical,miscellaneous',
            'description'  => 'required|string|max:255',
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'match_id'     => 'nullable|exists:matches,id',
            'receipt'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'notes'        => 'nullable|string',
        ]);

        $data['paid_by'] = auth()->id();

        if ($request->hasFile('receipt')) {
            $data['receipt_url'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create($data);
        AuditLog::record('expense_created', $expense, [], $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Expense recorded successfully.',
            'data' => $expense
        ], 201);
    }
}
