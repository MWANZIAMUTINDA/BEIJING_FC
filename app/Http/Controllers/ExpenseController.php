<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['paidBy', 'match'])
            ->latest()->paginate(20);

        $totalExpenses      = Expense::where('is_approved', true)->sum('amount');
        $totalContributions = Payment::where('status', 'confirmed')->sum('amount');
        $netBalance         = $totalContributions - $totalExpenses;

        $byCategory = Expense::where('is_approved', true)
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category')->get();

        // Calculate Monthly Trends (Approved Expenses) for the past 6 months
        $monthlyTotals = Expense::where('is_approved', true)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        return view('expenses.index', compact('expenses', 'totalExpenses', 'totalContributions', 'netBalance', 'byCategory', 'monthlyTotals'));
    }

    public function create()
    {
        $matches = \App\Models\FootballMatch::orderByDesc('match_date')->take(20)->get();
        return view('expenses.create', compact('matches'));
    }

    public function store(Request $request)
    {
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

        return redirect()->route('expenses.index')
            ->with('success', 'Expense of KSh ' . number_format($data['amount']) . ' recorded!');
    }

    public function approve(Expense $expense)
    {
        $expense->update(['is_approved' => !$expense->is_approved, 'approved_by' => auth()->id()]);
        AuditLog::record('expense_approval_toggled', $expense);
        return back()->with('success', 'Expense status updated.');
    }

    public function destroy(Expense $expense)
    {
        AuditLog::record('expense_deleted', $expense);
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
