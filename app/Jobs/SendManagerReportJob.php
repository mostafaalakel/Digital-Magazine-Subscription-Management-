<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ReportService;
use App\Mail\ManagerReportMail;
use Illuminate\Support\Facades\Mail;

class SendManagerReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(ReportService $reportService)
    {
        $reportData = $reportService->generateReport();

        $managerEmail = 'admin@gmail.com';
        Mail::to($managerEmail)->send(new ManagerReportMail($reportData));
    }
}
