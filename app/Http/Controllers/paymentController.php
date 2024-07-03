<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class PaymentController extends Controller
{
    var $webHookVerifyToken;

    public function __construct()
    {
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
        $this->webHookVerifyToken = env('XENDIT_WEBHOOK_VERIFY_TOKEN');
    }

    public function index()
    {
        // Method to list all payments (if required)
    }

    public function create(Request $request)
    {
        $apiInstance = new InvoiceApi();
        // Validasi request
        $request->validate([
            'notrans' => 'required|string',
            'type' => 'required|string',
            'plat' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        // Parameter untuk membuat invoice di Xendit
        $params = [
            'external_id' => (string) Str::uuid(),
            'description' => 'Pembayaran parkir via e-wallet untuk ' . $request->plat,
            'amount' => $request->amount,
            'invoice_duration' => '300', //5 menit
            'locale' => 'id',
            'currency' => 'IDR',
        ];
        $create_invoice_request = new \Xendit\Invoice\CreateInvoiceRequest($params);

        try {
            $result = $apiInstance->createInvoice($create_invoice_request, null);
        } catch (\Xendit\XenditSdkException $e) {
            Log::error('Exception when calling InvoiceApi->createInvoice: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }

        // Simpan data pembayaran ke database
        $payment = new Payment();
        $payment->notrans = $request->notrans;
        $payment->type = $request->type;
        $payment->plat = $request->plat;
        $payment->biaya =  $request->amount;
        $payment->masuk = Carbon::now();
        $payment->jenis = 'e-wallet';
        $payment->checkout_link = $result['invoice_url'];
        $payment->external_id = $params['external_id'];
        $payment->status = 'pending';
        $payment->save();

        // Return response
        return response()->json([
            'data' => $result['invoice_url'],
        ], 200);
    }

    public function refresh(String $notransaksi)
    {
        //Dari nomor transaksi, cari external id paling terbaru
        $payment = Payment::where([
            "notrans" => $notransaksi,
        ])
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->firstOrFail();

        $apiInstance = new InvoiceApi();

        $external_id =  $payment->external_id;
        try {

            $result = $apiInstance->getInvoices("", $external_id);
            if(count($result) > 0 ){
                
                $status = $result[0]->getStatus();
                $biaya = $result[0]->getAmount();

                if ((int)($payment->biaya) == $biaya) {
                    //Update table payment sesuai hasil dari result
                    $payment->status = strtolower($status);
                    $payment->save();

                    return response()->json(
                        $payment
                    , 200);
                } else {
                    return response()->json([
                        'error' => "Jumlah biaya yang harus dibayar tidak sama"
                    ], 400);
                }
            }else{
                return response()->json([
                    'error' => "Xendit tidak menemukan data"
                ], 400);
            }
        } catch (\Xendit\XenditSdkException $e) {
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
            'amount' => 'required|numeric',
        ]);

        // Simpan data pembayaran tunai ke database
        $payment = new Payment();
        $payment->notrans = $request->notrans;
        $payment->type = $request->type;
        $payment->plat = $request->plat;
        $payment->biaya = $request->amount;
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


    public function webHook(Request $request){
        $webhooktoken = $request->header('X-Callback-Token');

        if($webhooktoken !== $this->webHookVerifyToken){
            return response()->json([
                'message' => 'error',
                'reason' =>'Webhook Token not match !'
            ], 401);
        }

        $request->validate([
            'external_id' => 'required|string',
            'status' => 'required|string',
            'amount'=>'required|numeric'
        ]);
        
        $payment = Payment::where([
            "external_id" => $request->external_id,
        ])
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->firstOrFail();

        $payment->status = strtolower($request->status);
        $payment->save();    

        return response()->json([
            'message' => 'saved',
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
