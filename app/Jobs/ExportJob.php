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

/*
 * 队列中不建议使用单例模式，因为是常驻内存的
 * 当前调用命令为 php artisan queue:listen --queue=ExportJob
 * TODO 后续抛弃单例模式
 */
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

        if (!$className || !$actionName || !$params) throw new \Exception('导出参数有问题');

        $user = UserModel::find($this->downloadLog['creator_id']);
        $user && Auth::login($user);

        switch ($params['exportType']) {
            case 2:
                $this->export2local($className, $actionName, $params);
                break;
            case 3:
                $this->singleton($className, $actionName, $params);
                break;
            case 4:
                $this->singleton2($className, $actionName, $params);
                break;
        }
    }

    /**
     * 异步导出 限制1000条
     * @param $className
     * @param $actionName
     * @param $params
     */
    private function export2local($className, $actionName, $params)
    {
        $params['offset'] = 0;
        $params['limit'] = 1000;

        $params = (new Request())->merge($params);
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

    /**
     * 单例模式 + chunkById
     * 优点：减轻了参数校验
     * 缺点：代码侵入大
     * @param $className
     * @param $actionName
     * @param $params
     */
    private function singleton($className, $actionName, $params)
    {
        $params['downloadLogId'] = $this->downloadLog['id'];

        $params = (new Request())->merge($params);
        (new $className)->$actionName($params);
    }

    /**
     * 单例模式 + total
     * 优点：代码侵入小
     * 缺点：参数校验会更多
     * @param $className
     * @param $actionName
     * @param $params
     * @throws \Exception
     */
    private function singleton2($className, $actionName, $params)
    {
        $params['downloadLogId'] = $this->downloadLog['id'];

        $defaultLimit = $params['defaultLimit'] ?? 300;
        $times = ceil($params['total'] / $defaultLimit);

        for ($i = 0; $i < $times; $i++) {
            $params['offset'] = $i * $defaultLimit;
            $params['limit'] = $defaultLimit;
            $params['isLast'] = $i == ($times - 1);

            $params = (new Request())->merge($params);
            (new $className)->$actionName($params);
        }
    }

}
