<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    private string $env;
    private string $consumerKey;
    private string $consumerSecret;
    private string $shortcode;
    private string $passkey;
    private string $callbackUrl;

    public function __construct()
    {
        $this->env            = config('mpesa.env', 'sandbox');
        $this->consumerKey    = config('mpesa.consumer_key');
        $this->consumerSecret = config('mpesa.consumer_secret');
        $this->shortcode      = config('mpesa.shortcode');
        $this->passkey        = config('mpesa.passkey');
        $this->callbackUrl    = config('mpesa.callback_url');
    }

    private function baseUrl(): string
    {
        return $this->env === 'production'
            ? 'https://api.safaricom.co.ke'
            : 'https://sandbox.safaricom.co.ke';
    }

    public function getAccessToken(): ?string
    {
        return Cache::remember('mpesa_token', 3500, function () {
            try {
                $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                    ->get("{$this->baseUrl()}/oauth/v1/generate?grant_type=client_credentials");

                if ($response->successful()) {
                    return $response->json('access_token');
                }
            } catch (\Exception $e) {
                Log::error('M-Pesa token error: ' . $e->getMessage());
            }
            return null;
        });
    }

    public function stkPush(string $phone, float $amount, string $accountRef = null): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Could not get M-Pesa access token.'];
        }

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $phone     = $this->formatPhone($phone);

        try {
            $response = Http::withToken($token)
                ->post("{$this->baseUrl()}/mpesa/stkpush/v1/processrequest", [
                    'BusinessShortCode' => $this->shortcode,
                    'Password'          => $password,
                    'Timestamp'         => $timestamp,
                    'TransactionType'   => 'CustomerPayBillOnline',
                    'Amount'            => intval($amount),
                    'PartyA'            => $phone,
                    'PartyB'            => $this->shortcode,
                    'PhoneNumber'       => $phone,
                    'CallBackURL'       => $this->callbackUrl,
                    'AccountReference'  => $accountRef ?? config('mpesa.account_reference'),
                    'TransactionDesc'   => config('mpesa.transaction_desc'),
                ]);

            if ($response->successful() && $response->json('ResponseCode') === '0') {
                return ['success' => true, 'data' => $response->json(), 'message' => 'STK Push sent. Complete payment on your phone.'];
            }

            return ['success' => false, 'message' => $response->json('errorMessage', 'STK Push failed.'), 'data' => $response->json()];
        } catch (\Exception $e) {
            Log::error('M-Pesa STK Push error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'M-Pesa service error. Try again later.'];
        }
    }

    public function validateWebhook(array $payload): bool
    {
        // In production, validate signature/checksum from Daraja
        return isset($payload['Body']['stkCallback']);
    }

    public function parseWebhookPayload(array $payload): ?array
    {
        $callback = $payload['Body']['stkCallback'] ?? null;
        if (!$callback || $callback['ResultCode'] !== 0) {
            return null; // Payment failed or cancelled
        }

        $items = collect($callback['CallbackMetadata']['Item'] ?? [])
            ->keyBy('Name');

        return [
            'mpesa_code'             => $items->get('MpesaReceiptNumber')['Value'] ?? null,
            'amount'                 => $items->get('Amount')['Value'] ?? 0,
            'phone'                  => $items->get('PhoneNumber')['Value'] ?? null,
            'mpesa_receipt_number'   => $items->get('MpesaReceiptNumber')['Value'] ?? null,
            'transaction_date'       => $items->get('TransactionDate')['Value'] ?? null,
            'checkout_request_id'    => $callback['CheckoutRequestID'] ?? null,
        ];
    }

    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '254')) {
            $phone = '254' . $phone;
        }
        return $phone;
    }
}
