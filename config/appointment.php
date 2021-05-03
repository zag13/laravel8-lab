<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/26
 * Time: 2:14 下午
 */

return [
    // 下载类型
    'exportType' => [
        'browser' => [
            1   // 输出到浏览器
        ],
        'local' => [
            2,  // 异步下载至服务器
            3,  // 大数据下载
            4   // 另一种大数据下载思想
        ]
    ]

];
