<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/18
 * Time: 4:52 下午
 */


namespace App\Utils;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Storage;

class CommonUtils
{
    /**
     * 存储网络资源到本地
     * @param string $url
     * @param string $tmp
     * @return string
     */
    public static function storageFromUrl($url = '', $tmp = '')
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

    /**
     * 递归删除目录
     * @param string $src
     * @return bool
     */
    public static function rrmDir($src = '')
    {
        if (empty($src)) return true;

        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $full = $src . '/' . $file;
                if (is_dir($full)) {
                    self::rrmDir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

}