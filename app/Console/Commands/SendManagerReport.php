<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendManagerReportJob;

class SendManagerReport extends Command
{
    protected $signature = 'report:send-manager';

    protected $description = 'Send a weekly report to the manager.';

    public function handle()
    {
        SendManagerReportJob::dispatch();
        $this->info('Weekly report job dispatched successfully.');
    }
}

