<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\MemberBalance;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\FootballMatch;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'match'])->latest();

        if (auth()->user()->isMember()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('type'))    $query->where('type', $request->type);
        if ($request->filled('user_id') && (auth()->user()->isAdmin() || auth()->user()->isTreasurer())) {
            $query->where('user_id', $request->user_id);
        }

        $payments = $query->paginate(20);
        $members  = (auth()->user()->isAdmin() || auth()->user()->isTreasurer()) 
            ? User::where('role', 'member')->orderBy('name')->get() 
            : collect();

        $summary = [
            'total_confirmed' => Payment::where('status', 'confirmed')->when(auth()->user()->isMember(), fn($q) => $q->where('user_id', auth()->id()))->sum('amount'),
            'total_pending'   => Payment::where('status', 'pending')->when(auth()->user()->isMember(), fn($q) => $q->where('user_id', auth()->id()))->sum('amount'),
            'count_today'     => Payment::whereDate('created_at', today())->when(auth()->user()->isMember(), fn($q) => $q->where('user_id', auth()->id()))->count(),
        ];

        return view('payments.index', compact('payments', 'members', 'summary'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->where('is_active', true)->orderBy('name')->get();
        $matches = FootballMatch::upcoming()->get();
        return view('payments.create', compact('members', 'matches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'amount'     => 'required|numeric|min:1',
            'type'       => 'required|in:monthly,match,partial,penalty',
            'mpesa_code' => 'nullable|string|max:20|unique:payments,mpesa_code',
            'phone'      => 'required|string|max:20',
            'match_id'   => 'nullable|exists:matches,id',
            'notes'      => 'nullable|string',
        ]);

        $data['status']      = 'confirmed';
        $data['recorded_by'] = auth()->id();

        $payment = Payment::create($data);

        // Update member balance
        $this->updateMemberBalance($data['user_id']);

        AuditLog::record('payment_recorded', $payment, [], $data);

        return redirect()->route('payments.index')
            ->with('success', "Payment of KSh " . number_format($data['amount']) . " recorded successfully!");
    }

    public function mpesaWebhook(Request $request)
    {
        $payload = $request->all();
        $service = new MpesaService();

        if (!$service->validateWebhook($payload)) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid request'], 400);
        }

        $parsed = $service->parseWebhookPayload($payload);
        if (!$parsed) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Payment failed, ignored.']);
        }

        // Duplicate check
        if (Payment::where('mpesa_code', $parsed['mpesa_code'])->exists()) {
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Duplicate, ignored.']);
        }

        // Find member by phone
        $phone = substr((string) $parsed['phone'], -9); // last 9 digits
        $user  = User::where('phone', 'like', '%' . $phone)->first();

        $payment = Payment::create([
            'user_id'               => $user?->id,
            'amount'                => $parsed['amount'],
            'type'                  => 'monthly',
            'status'                => 'confirmed',
            'mpesa_code'            => $parsed['mpesa_code'],
            'phone'                 => $parsed['phone'],
            'mpesa_receipt_number'  => $parsed['mpesa_receipt_number'],
            'transaction_date'      => $parsed['transaction_date'],
        ]);

        if ($user) {
            $this->updateMemberBalance($user->id);
            AuditLog::record('mpesa_payment_received', $payment);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted successfully.']);
    }

    public function reconcile(Request $request)
    {
        $payment = Payment::findOrFail($request->payment_id);
        $payment->update(['status' => $request->status, 'notes' => $request->notes]);

        if ($request->status === 'confirmed') {
            $this->updateMemberBalance($payment->user_id);
        }

        AuditLog::record('payment_reconciled', $payment);

        return back()->with('success', 'Payment status updated.');
    }

    public function exportCsv()
    {
        $payments = Payment::with('user')->where('status', 'confirmed')->latest()->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename=payments_' . date('Y-m-d') . '.csv'];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Member', 'Phone', 'Amount', 'Type', 'M-Pesa Code', 'Date']);
            foreach ($payments as $p) {
                fputcsv($handle, [$p->id, $p->user?->name, $p->phone, $p->amount, $p->type, $p->mpesa_code, $p->created_at->format('d/m/Y H:i')]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function receipt(Payment $payment)
    {
        // Ensure access: owns payment or is admin/treasurer
        if ($payment->user_id !== auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isTreasurer()) {
            abort(403, 'Unauthorized access.');
        }

        $payment->load(['user', 'recordedBy']);
        return view('payments.receipt', compact('payment'));
    }

    public function statement(Request $request, User $user = null)
    {
        $user = $user ?? auth()->user();

        // Ensure access
        if ($user->id !== auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isTreasurer()) {
            abort(403, 'Unauthorized access.');
        }

        $ledger = collect();

        // 1. Confirmed payments (Credits)
        $payments = Payment::where('user_id', $user->id)->where('status', 'confirmed')->get();
        foreach ($payments as $p) {
            $ledger->push([
                'date'        => $p->created_at,
                'description' => 'Payment Credit: ' . $p->getTypeLabel(),
                'ref'         => $p->mpesa_code ?? "REC-{$p->id}",
                'debit'       => 0,
                'credit'      => $p->amount,
            ]);
        }

        // 2. Debits based on billing type
        if ($user->billing_type === 'monthly') {
            $start = $user->date_joined ?? $user->created_at ?? now();
            $end = now();
            $current = $start->copy()->startOfMonth();
            while ($current->lte($end)) {
                $ledger->push([
                    'date'        => $current->copy()->endOfMonth(),
                    'description' => 'Monthly Charge: ' . $current->format('F Y'),
                    'ref'         => 'SUB-' . $current->format('Ym'),
                    'debit'       => 2080,
                    'credit'      => 0,
                ]);
                $current->addMonth();
            }
        } else {
            $matches = FootballMatch::where('status', 'completed')
                ->whereHas('availabilities', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('status', 'available');
                })->get();

            foreach ($matches as $match) {
                $fee = $match->match_fee > 0 ? $match->match_fee : 350;
                $ledger->push([
                    'date'        => $match->match_date,
                    'description' => 'Match Debit: vs ' . $match->opponent,
                    'ref'         => 'MATCH-' . $match->id,
                    'debit'       => $fee,
                    'credit'      => 0,
                ]);
            }
        }

        // Sort chronologically
        $ledger = $ledger->sortBy('date')->values();

        // Calculate running balance
        $runningBalance = 0;
        $ledger = $ledger->map(function ($entry) use (&$runningBalance) {
            $runningBalance += ($entry['credit'] - $entry['debit']);
            $entry['running_balance'] = $runningBalance;
            return $entry;
        });

        $user->load('balance');

        return view('payments.statement', compact('user', 'ledger'));
    }

    public function treasurerReport()
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isTreasurer()) {
            abort(403, 'Unauthorized access.');
        }

        $totalIncome = Payment::where('status', 'confirmed')->sum('amount');
        $totalExpenses = Expense::where('is_approved', true)->sum('amount');
        $netFunds = $totalIncome - $totalExpenses;

        $typeBreakdown = Payment::where('status', 'confirmed')
            ->selectRaw('type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('type')->get()->keyBy('type');

        // Status counts for members based on MemberBalance standing
        $balances = MemberBalance::with('user')->get();
        $statusCounts = [
            'Paid'        => 0,
            'Pending'     => 0,
            'Partial'     => 0,
            'Outstanding' => 0,
        ];
        $totalOutstandingDebt = 0;

        foreach ($balances as $bal) {
            $statusCounts[$bal->getStatusLabel()]++;
            if ($bal->balance < 0) {
                $totalOutstandingDebt += abs($bal->balance);
            }
        }

        // Collection efficiency: total collected / (total collected + outstanding debt)
        $potentialIncome = $totalIncome + $totalOutstandingDebt;
        $efficiency = $potentialIncome > 0 ? round(($totalIncome / $potentialIncome) * 100) : 100;

        return view('payments.treasurer_report', compact(
            'totalIncome', 'totalExpenses', 'netFunds', 'typeBreakdown',
            'statusCounts', 'totalOutstandingDebt', 'efficiency', 'balances'
        ));
    }

    private function updateMemberBalance(int $userId): void
    {
        MemberBalance::recalculate($userId);
    }
}
