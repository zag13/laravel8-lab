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

        if (empty($errors)) {
            return true;
        } else {
            throw new \Exception($errors);
        }
    }

    public function respSuccess($data = [], $msg = 'ok')
    {
        return response()->json([
            'code' => 10000,
            'msg' => $msg,
            'data' => $data
        ]);
    }

    public function respFail($msg = 'error', $code = 0)
    {
        return response()->json([
            'code' => $code,
            'msg' => $msg,
            'data' => null
        ]);
    }
}
