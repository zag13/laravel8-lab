<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/17
 * Time: 2:31 下午
 */


namespace App\Http\Controllers\Test;


use App\Http\Controllers\Core\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function user(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $params = request()->all();

        $data = User::where('id', '=', $params['id'])->first()->toArray();

        return response()->json($data);
    }
}