<?php

namespace App\Http\Controllers\Core;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 重写 validate ，统一验证格式
     * @param Request $request
     * @param array   $rules
     * @param array   $messages
     * @param array   $customAttributes
     * @return bool
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $errors = Validator::make($request->all(), $rules, $messages, $customAttributes)->errors()->first();

        if (empty($errors)) return true;

        throw new \Exception($errors);
    }

    /**
     * 自定义成功返回格式
     * @param array  $data
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    public function respSuccess($data = [], $msg = 'ok')
    {
        return response()->json([
            'code' => 10000,
            'msg' => $msg,
            'data' => $data
        ]);
    }

    /**
     * 自定义失败返回格式
     * @param string $msg
     * @param int    $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function respFail($msg = 'error', $code = 0)
    {
        return response()->json([
            'code' => $code,
            'msg' => $msg
        ]);
    }
}
