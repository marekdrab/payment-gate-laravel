<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Payment;
use App\Service\PaymentService;
use App\Events\PaymentStatusChanged;

class PaymentController extends Controller
{
    private function createPayment(Request $request) {

        $data = $request->validate([
            'name' => ['required', 'string'],
            'surname' => ['required', 'string'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string'],
            'provider' => ['required', 'string'],
        ]);

        /**
         * Create find customer based email or name
         *  **/
        //$customer = Customer::where('email', $data['email'])->first();
        $customer = new Customer((object)[
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'address' => $data['address'],
        ]);

        switch (strtolower($data['currency'])){
            case "eur":
                $currency = Payment::CURRENCY_EUR;
                break;
            case "gbp":
                $currency = Payment::CURRENCY_GBP;
                break;
            case "usd":
                $currency = Payment::CURRENCY_USD; 
                break;
            default:
                return false;
        }

        return new Payment((object)[
            'payer' => $customer,
            'amount' => $data['amount'],
            'currency' => $currency,
            'provider' => $data['provider']
        ]);
    }

    protected function makePayment(Request $request) {
        $payment = $this->createPayment($request);
        if (!$payment) {
            return response()->json([
                'message' => 'Payment failed.',
                'error' => 'Used currency is not supported'
            ]);
        }

        $response = $this->sendPaymentToProvider($payment);

        if ($response->status === 'success') {
            $oldStatus = $payment->getStatus();
            $payment->setStatus(Payment::STATUS_COMPLETED);
            $metadata = [
                'provider_transaction_id' => $response->transaction_id,
            ];
            event(new PaymentStatusChanged($payment, $oldStatus, $payment->getStatus(), $metadata));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Payment processed successfully.',
                'amount' => $payment->getAmount(),
                'currency' => $payment->getCurrency(),
                'transaction_id' => $response->transaction_id
            ]);
        } else {
            $oldStatus = $payment->getStatus();
            $payment->setStatus(Payment::STATUS_FAILED);

            $metadata = [
                'provider_message' => $response->error,
            ];
            event(new PaymentStatusChanged($payment, $oldStatus, $payment->getStatus(), $metadata));
            
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed.',
                'error' => $response->error,
            ]);
        }

    }

    private function sendPaymentToProvider(Payment $payment) {
        
        // implementation of api call to provider

        $success = (object)[
            'status' => 'success',
            'transaction_id' => '1234567890',
            'redirect_url' => '/verifie_expiration'
        ];

        $failed = (object)[
            'status' => 'failed',
            'error' => 'error message'
        ];

        return rand(0,1) == 1 ? $success : $failed;
    }

    protected function verifyExpiration(Request $request) {
        
        $data = $request->validate([
            'transaction_id' => ['required', 'string']
        ]);
        //Find payment by transaction_id in database
        //$payment = Payment::find($request->transaction_id)->first();

        //This is only because of not using any database
        $customer = new Customer((object)[
            'name' => 'Name',
            'surname' => 'Surname',
            'email' => 'mail@address.com',
            'address' => 'Address',
        ]);
        $payment = new Payment((object)[
            'payer' => $customer,
            'amount' => rand(1,1000),
            'currency' => Payment::CURRENCY_EUR,
            'provider' => 'provider'
        ]);

        if ($payment->getExpiresAt() < time()) {
            $oldStatus = $payment->getStatus();
            $payment->setStatus(Payment::STATUS_EXPIRED);

            $metadata = [
                'provider_message' => 'Payment expired.',
            ];
            event(new PaymentStatusChanged($payment, $oldStatus, $payment->getStatus(), $metadata));
            
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment expired.',
                'error' => 'Payment expired.',
            ]);
        }
        $oldStatus = $payment->getStatus();
            $payment->setStatus(Payment::STATUS_COMPLETED);

            $metadata = [
                'provider_message' => 'Payment expired.',
            ];
            event(new PaymentStatusChanged($payment, $oldStatus, $payment->getStatus(), $metadata));
            
        return response()->json([
            'status' => 'success',
            'message' => 'Payment verified.',
        ]);
    }
}