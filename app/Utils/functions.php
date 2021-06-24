<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/18
 * Time: 4:52 下午
 */

if (!function_exists('recursiveDelete')) {
    /**
     * 递归删除目录
     * @param string $dir
     * @throws Exception
     */
    function recursiveDelete(string $dir = '')
    {
        $whiteList = [
            storage_path()
        ];

        $access = false;
        foreach ($whiteList as $value) {
            if (strpos($dir, $value) === 0) {
                $access = true;
                break;
            }
        }

        if ($access === false) throw new Exception($dir . "当前目录不合法");

        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if (($file == ".") || ($file == "..")) {
                    continue;
                }
                if (is_dir($dir . '/' . $file)) {
                    // 递归
                    recursiveDelete($dir . '/' . $file);
                } else {
                    unlink($dir . '/' . $file); // 删除文件
                }
            }
            closedir($handle);
            rmdir($dir);
        }
    }
}

if (!function_exists('list2tree')) {
    /**
     * 列表结构转树状结构
     * @param        $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param int $root
     * @return array
     */
    function list2tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0): array
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
}

if (!function_exists('tree2list')) {
    /**
     * 树状结构转列表结构
     * @param        $tree
     * @param string $child
     * @param array $list
     * @return array
     */
    function tree2list2($tree, $child = 'children', $list = []): array
    {
        if (empty($list)) $list = [];

        foreach ($tree as $branch) {
            $tmp = $branch;
            if (isset($branch[$child])) unset($tmp[$child]);
            $list[] = $tmp;

            if (isset($branch[$child]) && !empty($branch[$child])) {
                $list = tree2list2($branch[$child], $child, $list);
            }
        }

        return $list;
    }
}

if (!function_exists('getSubtree')) {
    /**
     * 获取对应子树
     * @param        $tree
     * @param        $name
     * @param string $fileds
     * @param string $child
     * @return array
     */
    function getSubtree($tree, $name, $fileds = 'name', $child = 'children'): array
    {
        $legalTree = [];
        foreach ($tree as $branch) {
            if ($branch[$fileds] == $name) {
                $legalTree = $branch;
                break;
            }
            if (isset($branch[$child])) {
                $legalTree = getSubtree($branch[$child], $name, $fileds, $child);
                break;
            }
        }
        return $legalTree;
    }
}

if (!function_exists('formatBytes')) {
    /**
     * 将 byte 转为 B,KB,MB,GB,TB
     * @param int $size
     * @param int $type
     * @return string
     */
    function formatBytes(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $units[$i];
    }
}
