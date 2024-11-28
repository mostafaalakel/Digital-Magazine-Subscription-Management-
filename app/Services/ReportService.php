<?php
namespace App\Services;

use App\Models\Subscription;
use App\Models\Payment;

class ReportService
{

    public function generateReport(): array
    {
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $totalPayments = Payment::sum('amount');

        return [
            'activeSubscriptions' => $activeSubscriptions,
            'totalPayments' => $totalPayments,
        ];
    }
}
