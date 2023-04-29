<?php
namespace App\Listeners;

use App\Events\PaymentStatusChanged;
use Illuminate\Support\Facades\Log;

class LogPaymentStatusChange
{
    public function handle(PaymentStatusChanged $event)
    {
        $payment = $event->payment;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;
        $metadata = $event->metadata;

        $logMessage = "Payment status changed for payment {$payment->getId()} from {$oldStatus} to {$newStatus}";

        if (!empty($metadata)) {
            $logMessage .= " with metadata: " . json_encode($metadata);
        }
        Log::channel('payments')->info($logMessage);

    }
}
