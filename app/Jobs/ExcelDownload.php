<?php

namespace App\Jobs;

use App\Models\ModDownloadLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExcelDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $downloadLog;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(ModDownloadLog $downloadLog)
    {
        $this->downloadLog = $downloadLog;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        Log::debug($this->downloadLog);
        $className = $this->downloadLog['class_name'];
        $actionName = $this->downloadLog['action_name'];
        $params = (new Request())->merge(unserialize($this->downloadLog['params']));

        $res = (new $className)->$actionName($params);

        $data = [
            'file_name' => $res['fileName'],
            'file_type' => $res['fileType'],
            'file_size' => $res['fileSize'],
            'file_link' => $res['fileLink'],
            'status' => 1
        ];

        $this->downloadLog->update($data);
    }
}
