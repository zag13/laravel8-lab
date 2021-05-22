<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/26
 * Time: 2:21 下午
 */


namespace App\Models\Core;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model as CModel;

/**
 * App\Models\Core
 *
 * @mixin \Eloquent
 */
class Model extends CModel
{
    // 一种校验数据的思想
    public function scopeCheckPermission($query, $params)
    {
        // 权限校验
        return $query->whereIn();
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
