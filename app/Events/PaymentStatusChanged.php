<?php
namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    public $oldStatus;
    public $newStatus;
    public $metadata;

    public function __construct(Payment $payment, $oldStatus, $newStatus, $metadata = [])
    {
        $this->payment = $payment;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->metadata = $metadata;
    }
}
