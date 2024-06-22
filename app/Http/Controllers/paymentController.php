<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Xendit\Xendit;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct() {
        Xendit::setApiKey(env('XENDIT_SECRET_KEY'));
    }

    public function index()
    {
        // Method to list all payments (if required)
    }

    public function create(Request $request)
    {
        // Validasi request
        $request->validate([
            'notrans' => 'required|string',
            'type' => 'required|string',
            'plat' => 'required|string',
            'biaya' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);
        
        dd($request);

        // Parameter untuk membuat invoice di Xendit
        $params = [
            'external_id' => (string) Str::uuid(),
            'payer_email' => 'customer@domain.com', // Optional
            'description' => 'Pembayaran parkir via e-wallet',
            'amount' => $request->amount,
            'redirect_url' => 'https://8c6b-114-10-80-198.ngrok-free.app/success',
        ];

        try {
            // Buat invoice di Xendit
            $createInvoice = \Xendit\Invoice::create($params);

            // Simpan data pembayaran ke database
            $payment = new Payment();
            $payment->notrans = $request->notrans;
            $payment->type = $request->type;
            $payment->plat = $request->plat;
            $payment->biaya = $params['amount'];
            $payment->masuk = Carbon::now();
            $payment->jenis = 'e-wallet';
            $payment->checkout_link = $createInvoice['invoice_url'];
            $payment->external_id = $params['external_id'];
            $payment->status = 'pending';
            $payment->save();

            // Return response
            return response()->json([
                'data' => $createInvoice['invoice_url']
            ], 200);
        } catch (\Xendit\Exceptions\ApiException $e) {
            // Tangkap error dari Xendit
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function paymentCash(Request $request)
    {
        // Validasi request
        $request->validate([
            'notrans' => 'required|string',
            'type' => 'required|string',
            'plat' => 'required|string',
            'biaya' => 'required|numeric',
        ]);

        // Simpan data pembayaran tunai ke database
        $payment = new Payment();
        $payment->notrans = $request->notrans;
        $payment->type = $request->type;
        $payment->plat = $request->plat;
        $payment->biaya = $request->biaya;
        $payment->masuk = Carbon::now();
        $payment->jenis = 'cash';
        $payment->external_id = (string) Str::uuid();
        $payment->checkout_link = 'Cash';
        $payment->status = 'settled'; // Ubah status ke 'settled' untuk pembayaran tunai
        $payment->save();

        // Return response
        return response()->json([
            'message' => 'Payment recorded successfully.',
            'data' => $payment
        ], 200);
    }

    public function show($id)
    {
        // Method to show a specific payment (if required)
    }

    public function update(Request $request, $id)
    {
        // Method to update a payment (if required)
    }

    public function destroy($id)
    {
        // Method to delete a payment (if required)
    }
}
