<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/17
 * Time: 2:31 下午
 */


namespace App\Http\Controllers\Test;


use App\Events\UserSendMessage;
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

    public function collect()
    {
        $array = [
            'a' => ['title' => 'a', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
            'b' => ['title' => 'b', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
            'c' => ['title' => 'c', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
            'd' => ['title' => 'a', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
        ];
        $data = collect($array)->reduce(function ($result, $item) {
            if ($result == null) {
                $result = [
                    'aaa' => 0,
                    'bbb' => 0,
                    'ccc' => 0
                ];
            }
            $result['aaa'] += $item['aaa'];
            $result['bbb'] += $item['bbb'];
            $result['ccc'] += $item['ccc'];

            return $result;
        });

        $data2 = collect($array)->groupBy('title')
            ->map(function ($value, $groupBy) {
                return [
                    'title' => $groupBy,
                    'aaa' => $value->sum('aaa'),
                    'bbb' => $value->sum('bbb'),
                    'ccc' => $value->sum('ccc'),
                ];
            })
            ->toArray();

        $data3 = 'a:55:{i:0;a:3:{s:5:"label";s:21:"子包活跃登录数";s:4:"prop";s:20:"adl_activeLoginCount";s:8:"disabled";b:1;}i:1;a:3:{s:5:"label";s:24:"子包活跃充值人数";s:4:"prop";s:20:"adl_userPaymentCount";s:8:"disabled";b:0;}i:2;a:3:{s:5:"label";s:24:"子包活跃充值金额";s:4:"prop";s:17:"adl_moneyPayCount";s:8:"disabled";b:0;}i:3;a:3:{s:5:"label";s:17:"子包活跃ARPPU";s:4:"prop";s:20:"adl_activeLoginARPPU";s:8:"disabled";b:0;}i:4;a:3:{s:5:"label";s:9:"激活数";s:4:"prop";s:13:"activateCount";s:8:"disabled";b:0;}i:5;a:3:{s:5:"label";s:21:"激活到登录界面";s:4:"prop";s:18:"activateLoginCount";s:8:"disabled";b:0;}i:6;a:3:{s:5:"label";s:21:"子包激活注册率";s:4:"prop";s:17:"adl_regActiveRate";s:8:"disabled";b:0;}i:7;a:3:{s:5:"label";s:9:"创角数";s:4:"prop";s:9:"roleCount";s:8:"disabled";b:0;}i:8;a:3:{s:5:"label";s:21:"子包注册创角率";s:4:"prop";s:15:"adl_regRoleRate";s:8:"disabled";b:0;}i:9;a:3:{s:5:"label";s:18:"子包注册人数";s:4:"prop";s:12:"adl_regCount";s:8:"disabled";b:0;}i:10;a:3:{s:5:"label";s:24:"子包注册排重设备";s:4:"prop";s:18:"adl_regDeviceCount";s:8:"disabled";b:0;}i:11;a:3:{s:5:"label";s:24:"子包注册充值人数";s:4:"prop";s:23:"adl_newUserPaymentCount";s:8:"disabled";b:0;}i:12;a:3:{s:5:"label";s:24:"子包注册充值金额";s:4:"prop";s:20:"adl_newMoneyPayCount";s:8:"disabled";b:0;}i:13;a:3:{s:5:"label";s:19:"子包注册数ARPU";s:4:"prop";s:16:"adl_regCountARPU";s:8:"disabled";b:0;}i:14;a:3:{s:5:"label";s:20:"子包注册数ARPPU";s:4:"prop";s:17:"adl_regCountARPPU";s:8:"disabled";b:0;}i:15;a:3:{s:5:"label";s:18:"子包注册次留";s:4:"prop";s:11:"adl_staySec";s:8:"disabled";b:0;}i:16;a:3:{s:5:"label";s:21:"母包活跃登录数";s:4:"prop";s:21:"game_activeLoginCount";s:8:"disabled";b:0;}i:17;a:3:{s:5:"label";s:24:"母包活跃充值人数";s:4:"prop";s:21:"game_userPaymentCount";s:8:"disabled";b:0;}i:18;a:3:{s:5:"label";s:24:"母包活跃充值金额";s:4:"prop";s:18:"game_moneyPayCount";s:8:"disabled";b:0;}i:19;a:3:{s:5:"label";s:17:"母包活跃ARPPU";s:4:"prop";s:21:"game_activeLoginARPPU";s:8:"disabled";b:0;}i:20;a:3:{s:5:"label";s:21:"母包激活注册率";s:4:"prop";s:18:"game_regActiveRate";s:8:"disabled";b:0;}i:21;a:3:{s:5:"label";s:21:"母包注册创角率";s:4:"prop";s:16:"game_regRoleRate";s:8:"disabled";b:0;}i:22;a:3:{s:5:"label";s:18:"母包注册人数";s:4:"prop";s:13:"game_regCount";s:8:"disabled";b:0;}i:23;a:3:{s:5:"label";s:24:"母包注册排重设备";s:4:"prop";s:19:"game_regDeviceCount";s:8:"disabled";b:0;}i:24;a:3:{s:5:"label";s:24:"母包注册充值人数";s:4:"prop";s:24:"game_newUserPaymentCount";s:8:"disabled";b:0;}i:25;a:3:{s:5:"label";s:24:"母包注册充值金额";s:4:"prop";s:21:"game_newMoneyPayCount";s:8:"disabled";b:0;}i:26;a:3:{s:5:"label";s:19:"母包注册数ARPU";s:4:"prop";s:17:"game_regCountARPU";s:8:"disabled";b:0;}i:27;a:3:{s:5:"label";s:20:"母包注册数ARPPU";s:4:"prop";s:18:"game_regCountARPPU";s:8:"disabled";b:0;}i:28;a:3:{s:5:"label";s:18:"母包注册次留";s:4:"prop";s:12:"game_staySec";s:8:"disabled";b:0;}i:29;a:3:{s:5:"label";s:21:"游戏活跃登录数";s:4:"prop";s:19:"cp_activeLoginCount";s:8:"disabled";b:0;}i:30;a:3:{s:5:"label";s:24:"游戏活跃充值人数";s:4:"prop";s:19:"cp_userPaymentCount";s:8:"disabled";b:0;}i:31;a:3:{s:5:"label";s:24:"游戏活跃充值金额";s:4:"prop";s:16:"cp_moneyPayCount";s:8:"disabled";b:0;}i:32;a:3:{s:5:"label";s:17:"游戏活跃ARPPU";s:4:"prop";s:19:"cp_activeLoginARPPU";s:8:"disabled";b:0;}i:33;a:3:{s:5:"label";s:21:"游戏激活注册率";s:4:"prop";s:16:"cp_regActiveRate";s:8:"disabled";b:0;}i:34;a:3:{s:5:"label";s:21:"游戏注册创角率";s:4:"prop";s:14:"cp_regRoleRate";s:8:"disabled";b:0;}i:35;a:3:{s:5:"label";s:18:"游戏注册人数";s:4:"prop";s:11:"cp_regCount";s:8:"disabled";b:0;}i:36;a:3:{s:5:"label";s:24:"游戏注册排重设备";s:4:"prop";s:17:"cp_regDeviceCount";s:8:"disabled";b:0;}i:37;a:3:{s:5:"label";s:24:"游戏注册充值人数";s:4:"prop";s:22:"cp_newUserPaymentCount";s:8:"disabled";b:0;}i:38;a:3:{s:5:"label";s:24:"游戏注册充值金额";s:4:"prop";s:19:"cp_newMoneyPayCount";s:8:"disabled";b:0;}i:39;a:3:{s:5:"label";s:19:"游戏注册数ARPU";s:4:"prop";s:15:"cp_regCountARPU";s:8:"disabled";b:0;}i:40;a:3:{s:5:"label";s:20:"游戏注册数ARPPU";s:4:"prop";s:16:"cp_regCountARPPU";s:8:"disabled";b:0;}i:41;a:3:{s:5:"label";s:18:"游戏注册次留";s:4:"prop";s:10:"cp_staySec";s:8:"disabled";b:0;}i:42;a:3:{s:5:"label";s:21:"账号活跃登录数";s:4:"prop";s:19:"ac_activeLoginCount";s:8:"disabled";b:0;}i:43;a:3:{s:5:"label";s:24:"账号活跃充值人数";s:4:"prop";s:19:"ac_userPaymentCount";s:8:"disabled";b:0;}i:44;a:3:{s:5:"label";s:24:"账号活跃充值金额";s:4:"prop";s:16:"ac_moneyPayCount";s:8:"disabled";b:0;}i:45;a:3:{s:5:"label";s:17:"账号活跃ARPPU";s:4:"prop";s:19:"ac_activeLoginARPPU";s:8:"disabled";b:0;}i:46;a:3:{s:5:"label";s:21:"账号激活注册率";s:4:"prop";s:16:"ac_regActiveRate";s:8:"disabled";b:0;}i:47;a:3:{s:5:"label";s:21:"账号注册创角率";s:4:"prop";s:14:"ac_regRoleRate";s:8:"disabled";b:0;}i:48;a:3:{s:5:"label";s:18:"账号注册人数";s:4:"prop";s:11:"ac_regCount";s:8:"disabled";b:0;}i:49;a:3:{s:5:"label";s:24:"账号注册排重设备";s:4:"prop";s:17:"ac_regDeviceCount";s:8:"disabled";b:0;}i:50;a:3:{s:5:"label";s:24:"账号注册充值人数";s:4:"prop";s:22:"ac_newUserPaymentCount";s:8:"disabled";b:0;}i:51;a:3:{s:5:"label";s:24:"账号注册充值金额";s:4:"prop";s:19:"ac_newMoneyPayCount";s:8:"disabled";b:0;}i:52;a:3:{s:5:"label";s:19:"账号注册数ARPU";s:4:"prop";s:15:"ac_regCountARPU";s:8:"disabled";b:0;}i:53;a:3:{s:5:"label";s:20:"账号注册数ARPPU";s:4:"prop";s:16:"ac_regCountARPPU";s:8:"disabled";b:0;}i:54;a:3:{s:5:"label";s:18:"账号注册次留";s:4:"prop";s:10:"ac_staySec";s:8:"disabled";b:0;}}';
        $data3 = collect(unserialize($data3))->filter(function ($value) {
            if ($value['disabled'] == false) return $value;
        })->pluck('prop')->all();

        var_dump($data, $data2, $data3);
    }

    public function broadcast()
    {
        $user = User::find(1);
        $message = 'hello,world!';
        event(new UserSendMessage($user, $message));
    }

}
