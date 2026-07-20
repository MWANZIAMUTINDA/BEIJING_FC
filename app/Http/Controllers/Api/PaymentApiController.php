<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\MemberBalance;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class PaymentApiController extends Controller
{
    /**
     * Get member's statement, billing type, and balance standing.
     */
    public function index()
    {
        $user = auth()->user();
        $user->load('balance');

        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'billing_type' => $user->billing_type,
                'balance' => $user->balance ? $user->balance->balance : 0,
                'total_paid' => $user->balance ? $user->balance->total_paid : 0,
                'total_owed' => $user->balance ? $user->balance->total_owed : 0,
                'history' => $payments
            ]
        ]);
    }

    /**
     * Trigger M-Pesa STK Push payment request from the mobile app.
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'phone' => 'required|string',
            'type' => 'required|in:monthly,match,partial,penalty',
            'match_id' => 'nullable|exists:matches,id',
        ]);

        $user = auth()->user();

        // Standardize phone format (e.g. 2547XXXXXXXX)
        $phone = $request->phone;
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        // Generate dynamic payment tracking row
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => $request->type,
            'status' => 'pending',
            'phone' => $phone,
            'match_id' => $request->match_id,
            'notes' => 'Initiated via Mobile App',
            'recorded_by' => $user->id,
        ]);

        // Integrate with MpesaService to dispatch STK Push request
        $mpesa = new MpesaService();
        $response = $mpesa->initiateStkPush($phone, $request->amount, $payment->id);

        if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
            return response()->json([
                'status' => 'success',
                'message' => 'STK Push dispatched to your phone. Enter your pin to complete.',
                'payment_id' => $payment->id,
                'merchant_request_id' => $response['MerchantRequestID'] ?? null,
                'checkout_request_id' => $response['CheckoutRequestID'] ?? null,
            ]);
        }

        // If API fails, record failure notes
        $payment->update(['status' => 'failed', 'notes' => 'STK Push Initiation Failed: ' . json_encode($response)]);

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to initiate M-Pesa STK push. Please try again.',
            'debug' => $response
        ], 500);
    }
}
