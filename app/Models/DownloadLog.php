<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/26
 * Time: 2:26 下午
 */


namespace App\Models;


use App\Models\Core\Model;

class DownloadLog extends Model
{
    public $table = "download_log";

    protected $fillable = [
        'class_name', 'action_name', 'params',
        'file_name', 'file_type', 'file_size', 'file_link',
        'error_message', 'creator_id', 'creator_name', 'status'
    ];

    protected $casts = [
        'creator_id' => 'int'
    ];

    /**
     * 测试一对一&&一对多关联（belongs）
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    /**
     * 测试 scope 方法
     * @param $query
     * @param $id
     * @return mixed
     */
    public function scopeCreator($query, $id)
    {
        return $query->where('creator_id', '=', $id);
    }
}
