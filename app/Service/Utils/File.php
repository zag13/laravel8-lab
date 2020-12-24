<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/24
 * Time: 11:06 下午
 */


namespace App\Service\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

class File
{
    /**
     * 存储网络资源到本地
     * @param string $url
     * @param string $tmp
     * @return string
     */
    public static function storageFromUrl($url = '', $tmp = ''): string
    {
        if (empty($url) || empty($tmp)) throw new \Exception('未传入 url 或 tmp');
        try {
            $client = new Client();
            $data = $client->request('get', $url)->getBody()->getContents();
            Storage::disk('local')->put($tmp, $data);
        } catch (GuzzleException $exception) {
            throw new \Exception($exception->getMessage());
        }
        return storage_path('app/') . $tmp;
    }

}