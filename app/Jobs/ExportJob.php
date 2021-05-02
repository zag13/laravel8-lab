<?php

namespace App\Jobs;

use App\Models\DownloadLogModel;
use App\Models\UserModel;
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

    public $tries = 1;

    protected $downloadLog;

    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct(DownloadLogModel $downloadLog)
    {
        $this->downloadLog = $downloadLog;
        $this->queue = 'ExportJob';
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $className = $this->downloadLog['class_name'];
        $actionName = $this->downloadLog['action_name'];

        $params = json_decode($this->downloadLog['params'], true);
        $params['downloadLogId'] = $this->downloadLog['id'];        // 大数据导出要使用
        $params = (new Request())->merge($params);

        $user = UserModel::find($this->downloadLog['creator_id']);
        $user && Auth::login($user);

        $res = (new $className)->$actionName($params);

        // 大数据导出时是没有返回 excel 文件数据的
        if ($res == true) return;

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
