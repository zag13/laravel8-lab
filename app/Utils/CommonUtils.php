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

    /**
     * 递归删除目录
     * @param string $src
     * @return bool
     */
    public static function rrmDir($src = ''): bool
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

    /**
     * 列表结构转树状结构
     * @param        $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int    $root
     * @return array
     */
    public static function list2tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0): array
    {
        $tree = [];
        if (!is_array($list)) return $tree;

        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $value) {
            $refer[$value[$pk]] = &$list[$key];
        }

        foreach ($list as $key => $value) {
            $parentId = $value[$pid];
            if ($parentId == $root) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }

        return $tree;
    }

    /**
     * 树状结构转列表结构
     * @param        $tree
     * @param string $child
     * @return array
     */
    public static function tree2list($tree, $child = 'children'): array
    {
        static $list = [];
        foreach ($tree as $branch) {
            $tmp = $branch;
            unset($tmp[$child]);
            $list[] = $tmp;
            if (isset($branch[$child]) && !empty($branch[$child])) {
                self::tree2list($branch[$child]);
            }
        }
        return $list;
    }

    /**
     * 获取对应的子树
     * @param        $tree
     * @param        $name
     * @param string $fileds
     * @param string $child
     * @return array|mixed
     */
    public static function getSubtree($tree, $name, $fileds = 'name', $child = 'children'): array
    {
        $legalTree = [];
        foreach ($tree as $branch) {
            if ($branch[$fileds] == $name) {
                $legalTree = $branch;
                break;
            }
            if (isset($branch[$child])) {
                $legalTree = self::getSubtree($branch[$child], $name, $fileds, $child);
                break;
            }
        }
        return $legalTree;
    }
}
