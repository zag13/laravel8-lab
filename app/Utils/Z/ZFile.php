<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/24
 * Time: 11:06 下午
 */


namespace App\Utils\Z;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

class ZFile
{
    /**
     * 存储网络资源到本地
     * @param string $url
     * @param string $tmp
     * @return string
     */
    public static function storageByUrl($url = '', $tmp = ''): string
    {
        if (empty($url) || empty($tmp)) throw new \Exception('未传入 url 或 tmp');

        $client = new Client();
        $data = $client->request('get', $url)->getBody()->getContents();
        Storage::disk('local')->put($tmp, $data);

        return storage_path('app/') . $tmp;
    }

}
