<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/26
 * Time: 2:26 下午
 */


namespace App\Models;


use App\Models\Core\Model;

class ModDownloadLog extends Model
{
    public $table = "download_log";

    protected $fillable = [
        'class_name', 'action_name', 'params',
        'file_name', 'file_type', 'file_size', 'file_link',
        'creator_id', 'creator_name', 'status'
    ];
}