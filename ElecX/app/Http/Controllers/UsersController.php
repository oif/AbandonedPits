<?php

namespace ElecX\Http\Controllers;

use Illuminate\Http\Request;

use ElecX\Http\Requests;
use ElecX\Http\Controllers\Controller;

use ElecX\User;
use ElecX\Dorm;
use ElecX\Invitation;
use Input, DB;

use ElecX\Services\RegExp;
use ElecX\Services\RegExpBuilder;

class UsersController extends Controller
{

    static function randStr($len=6) {
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $len; $i++)
        {
            $rand = mt_rand(0, $arr_len-1);
            $str.=$arr[$rand];
        }
        return $str;
    }

    public function code()
    {
        for ($i=0; $i < 10; $i++) {
            $invitation = new Invitation;
            $invitation->code = UsersController::randStr(32);
            $invitation->save();
            var_dump($invitation->code);
        }
    }

    public function inquiry(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required|digits:11'
        ]);

        $user = DB::table('users')->where('phone', Input::get('phone'))->first();;
        $dorm = Dorm::find($user->id);
        return view('inquiry')->withFlag(true)->withLatest("当前剩余电费：" . $dorm->balance . " 元，剩余电量：" . $dorm->remain_elec . " 度。");
    }

    public function join(Request $request)
    {
        $this->validate($request, [
            'nickname' => 'required|min:1|max:20|alpha_dash',
            'area' => 'required',
            'building' => 'required',
            'dorm' => 'required|digits_between:3,4',
            'phone' => 'required|digits:11',
            'code' => 'required|alpha_num'
        ]);

        $code = Invitation::where('code', '=', Input::get('code'))->count();

        if ($code == 0) {
            return view('join')->withFlag(true)->withNotice('少年，暗号不对啊');
        }

        DB::table('invitations')->where('code', '=', Input::get('code'))->delete();

        $user = new User;
        $user->nickname = Input::get('nickname');
        $user->phone = Input::get('phone');
        $user->issuing_date = Input::get('issuing_date');

        $dorm = new Dorm;
        $dorm->area = Input::get('area');
        $dorm->building = Input::get('building');
        $dorm->dorm = Input::get('dorm');

        if( strlen($dorm->dorm) != 4 )
        {
            $dorm->dorm = '0' . $dorm->dorm;
        }

        if ($user->save() && $dorm->save() ) {
            return view('join')->withFlag(true)->withNotice( $user->nickname . '，你已成功加入 ElecX 成员计划！所绑定宿舍电费缓存数据将在24小时内更新');
        } else {
            return view('join')->withFlag(true)->withNotice('小X跑出去浪了，赶紧告诉 Neo 把它抓回来打！');
        }
    }
}
