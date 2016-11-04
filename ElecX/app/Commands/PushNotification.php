<?php

namespace ElecX\Commands;

use ElecX\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use ElecX\Dorm;
use ElecX\User;

class PushNotification extends Command implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    public static function notificationPusher($phone, $nickname, $dorm, $balance, $remain_elec)
    {
        $statusStr = array(
            "0" => "短信发送成功",
            "-1" => "参数不全",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误",
            "40" => "账号不存在",
            "41" => "余额不足",
            "42" => "帐户已过期",
            "43" => "IP地址限制",
            "50" => "内容含有敏感词"
        );
        $smsapi = "http://api.smsbao.com/"; //短信网关
        $user = "neoz"; //短信平台帐号
        $pass = md5("woai1314"); //短信平台密码
        $content="Hi, " . $nickname . "。你所绑定的" . $dorm . "宿舍电费剩余 " . $balance . " 元，剩余电量" . $remain_elec . " 度。From ElecX";//要发送的短信内容
        $phone = $phone;//要发送短信的手机号码
        $sendurl = $smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result = file_get_contents($sendurl) ;
        return $statusStr[$result];
    }


    public function fire($job, $dorm_Id)
    {
        $dorm = Dorm::find($dorm_Id);
        $user = User::find($dorm_Id);

        PushNotification::notificationPusher($user->phone, $user->nickname, $dorm->dorm, $dorm->balance, $dorm->remain_elec);

        $dorm->notificationSwitch = true;
        $dorm->notificationSent = true;
        $dorm->notificationCount++;
        if (!($dorm->save())) {
            $dorm->save();
        }

        $job->delete();
    }
}
