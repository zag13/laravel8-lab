<?php

namespace App\Jobs;

use App\Models\DownloadLogModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $downloadLog;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(DownloadLogModel $downloadLog)
    {
        $this->downloadLog = $downloadLog;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $className = $this->downloadLog['class_name'];
        $actionName = $this->downloadLog['action_name'];
        $params = (new Request())->merge(json_decode($this->downloadLog['params'], true));

        $user = User::find($this->downloadLog['creator_id']);
        $user && Auth::login($user, true);

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
