<?php

namespace ElecX\Commands;

use ElecX\Commands\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use ElecX\Dorm;
use ElecX\Commands\simple_html_dom;
use ElecX\Services\Runtime;
use Cache;
use Carbon\Carbon;
use ElecX\Services\RegExp;
use ElecX\Services\RegExpBuilder;

class PullElec extends Command implements SelfHandling, ShouldQueue
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

    //获取验证
    public static function getValidationeCodes($content)
    {
        /*
        $__VIEWSTATE = $dom->getElementById("__VIEWSTATE")->innertext;
        $__EVENTVALIDATION = $dom->getElementById("__EVENTVALIDATION")->innertext;
        $dxgvSubInfo_CallbackState = $dom->getElementById("dxgvSubInfo_CallbackState")->innertext;
        $dxgvElec_CallbackState = $dom->getElementById("dxgvElec_CallbackState")->innertext;
        */
        $volidationTags = array('__VIEWSTATE', '__EVENTVALIDATION', 'dxgvSubInfo_CallbackState', 'dxgvElec_CallbackState');
        $validationCodes = array();
        $dom = new simple_html_dom();
        $dom->load($content);
        foreach ($volidationTags as $tag) {
           array_push($validationCodes,  $dom->getElementById($tag)->value);
       }
       return $validationCodes;
    }

    public static function getCookieJar()
    {
        if (Cache::has('cookie_jar')) {
            return Cache::get('cookie_jar');
        } else {
            $url='http://elec.xmu.edu.cn/PdmlWebSetup/Pages/SMSMain.aspx';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, 1);    //只获取header
            curl_setopt($curl, CURLOPT_NOBODY, 1);    //不获取body
            $content = curl_exec($curl);
            curl_close($curl);
            list($header, $body) = explode("\r\n\r\n", $content);
            preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
            $cookie = $matches[1];
            $expiresAt = Carbon::now()->addMinutes(20);
            Cache::put('cookie_jar', $cookie, $expiresAt);
            return $cookie;
        }
    }

    public static function getValidationes($area)
    {
        $url='http://elec.xmu.edu.cn/PdmlWebSetup/Pages/SMSMain.aspx';
        $todayTimestamp = ( mktime(0,0,0,date('m'),date('d'),date('Y')) + 28800 ) * 1000;
        //$yesterdayTimestamp = ( mktime(0,0,0,date('m'),date('d')-1,date('Y')) + 28800 ) * 1000;
        //获取初试post validation
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($curl);
        curl_close($curl);

        list($header, $body) = explode("\r\n\r\n", $content);
        preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
        $cookie = $matches[1];
        $expiresAt = Carbon::now()->addMinutes(20);
        Cache::put('cookie_jar', $cookie, $expiresAt);

        $validationCodes = PullElec::getValidationeCodes($content);
        //首轮结束
        //
        //post 获取园区validation
        $fields=array(
            '__EVENTTARGET'  =>  '\'drxiaoqu\'',
            '__EVENTARGUMENT'  =>  '\'\'',
            '__VIEWSTATE'  => $validationCodes[0],
            '__EVENTVALIDATION'  =>  $validationCodes[1],
            'drxiaoqu'  =>  $area,
            'drlou'  =>  '',
            'txtRoomid'  =>  '',
            'dxdateStart_Raw'  =>  $todayTimestamp,
            'dxdateEnd_Raw' =>  $todayTimestamp,
            'dxgvSubInfo$CallbackState'  =>  $validationCodes[2],
            'dxgvElec$CallbackState'  =>  $validationCodes[3],
            'DXScript'  =>  '1_42,1_74,2_22,2_29,1_46,1_54,2_21,1_67,1_64,2_16,2_15,1_52,1_65,3_7'
        );
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 0);    //不获取header
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //获取body
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
        ob_start();
        curl_exec($ch);
        $result=ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        $finalCodes = PullElec::getValidationeCodes($result);
        $expiresAt = Carbon::now()->addHours(12);
        for ($i=0; $i < 4; $i++) {
            Cache::put($area . '_' . $i, $finalCodes[$i], $expiresAt);
        }
        return $finalCodes;    //返回可用查询结果
    }

    public static function realtimeBalance($content)
    {
        $builder = new RegExpBuilder();
        $digits = array("0","1","2","3","4","5","6","7","8","9");
        $regExp = $builder
            ->multiLine()
            ->globalMatch()
            ->some($digits)
            ->then(".")
            ->some($digits)
            ->getRegExp();
        $matches = $regExp->findIn($content);
        return $matches;
    }

    //正式查询
    public static function pulling($area, $building, $dorm)
    {
        $validationCodes = array();
        $cookie = PullElec::getCookieJar();
        if (Cache::has($area . '_0')) {
            for ($i=0; $i < 4; $i++) {
                array_push($validationCodes, Cache::get($area . '_' . $i));
            }
        } else {
            $validationCodes = PullElec::getValidationes($area);
        }

        $todayTimestamp = ( mktime(0,0,0,date('m'),date('d'),date('Y')) + 28800 ) * 1000;
        //$yesterdayTimestamp = ( mktime(0,0,0,date('m'),date('d')-1,date('Y')) + 28800 ) * 1000;
        $url='http://elec.xmu.edu.cn/PdmlWebSetup/Pages/SMSMain.aspx';
        $fields=array(
            '__VIEWSTATE'  => $validationCodes[0],
            '__EVENTVALIDATION'  =>  $validationCodes[1],
            'dxgvSubInfo$CallbackState'  =>  $validationCodes[2],
            'dxgvElec$CallbackState'  =>  $validationCodes[3],
            'drxiaoqu'  =>  $area,
            'drlou'  =>  $building,
            'txtRoomid'  =>  $dorm,
            'dxdateStart_Raw'  =>  $todayTimestamp,
            'dxdateEnd_Raw' =>  $todayTimestamp,
            'DXScript'  =>  '1_42,1_74,2_22,2_29,1_46,1_54,2_21,1_67,1_64,2_16,2_15,1_52,1_65,3_7'
        );
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_HEADER, 0);    //不获取header
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //获取body
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
        ob_start();
        curl_exec($ch);
        $result=ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        $dom = new simple_html_dom();
        $dom->load($result);
        $ret = $dom->getElementById("lableft");
        return PullElec::realtimeBalance($ret);
    }

    public function fire($job, $dormId)
    {
        $notificationTriggerLine = 10;

        $dorm = Dorm::find($dormId);

        /*计时器启动*/
        //$runtime= new runtime;
        //$runtime->start();

        //开始检索宿舍电费信息
        $latestInfo = PullElec::pulling($dorm->area, $dorm->building, $dorm->dorm);

        /*计时器停止*/
        //$runtime->stop();
        //echo "页面执行时间: ".$runtime->spent()." 毫秒";

        $dorm->balance = $latestInfo[0];
        $dorm->remain_elec = $latestInfo[1];

        if ($dorm->balance <= $notificationTriggerLine) {
            $dorm->notificationSwitch = true;
        } else {
            if ($dorm->notificationSwitch && $dorm->notificationSent) {
                $dorm->notificationSwitch = false;
                $dorm->notificationSent = false;
            }
        }
        if (!($dorm->save())) {
            $dorm->save();
        }

        $job->delete();
    }
}
