<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use App\Alert, App\Trans;
use Cache;
use Lang;

// JPush API
use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class AlertsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $start = microtime(true);
        $url='http://content-zhb.warframe.com.cn/dynamic/worldState.php';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);    //不获取header
        curl_setopt($curl, CURLOPT_NOBODY, 0);    //只获取body
        $content = curl_exec($curl);
        curl_close($curl);
        $alerts = json_decode($content, TRUE)['Alerts'];
        /*
        foreach ($alerts as $x) {
            var_dump($x['MissionInfo']['missionReward']);
        }
        echo '<hr>';
        */
        $count = 0;
        if (count($alerts) > 0) {
            foreach ($alerts as $alert) {
                // Initialize
                if (DB::table('alerts')->select('id')->where('id', $alert['_id']['$id'])->first()) {
                    continue;
                }
                $_alert = new Alert();
                $_alert->id = $alert['_id']['$id'];
                $_alert->activation = $alert['Activation']['sec'];
                $_alert->expiry = $alert['Expiry']['sec'];
                $_alert->description = $alert['MissionInfo']['descText'];
                $_alert->location = $alert['MissionInfo']['location'];
                $_alert->type = $alert['MissionInfo']['missionType'];
                $_alert->faction = $alert['MissionInfo']['faction'];
                $_alert->level = $alert['MissionInfo']['minEnemyLevel'].' ~ '. $alert['MissionInfo']['maxEnemyLevel'];
                $_alert->credits = $alert['MissionInfo']['missionReward']['credits'];
                if (isset($alert['MissionInfo']['missionReward']['countedItems'])) {
                    $items = '';
                    foreach ($alert['MissionInfo']['missionReward']['countedItems'] as $item) {
                        $items .= $item['ItemType'];
                        $this->validateTranslate($item['ItemType']);
                        $rewardItem = $item['ItemType'];
                        $items .= ',';
                        $items .= $item['ItemCount'];
                        $items .= '|';
                    }
                    $_alert->items = substr($items, 0, -1);
                }
                if (isset($alert['MissionInfo']['missionReward']['items'])) {
                    $rewardItem = $alert['MissionInfo']['missionReward']['items'][0];
                    $_alert->items = $alert['MissionInfo']['missionReward']['items'][0] . ',1';
                    $this->validateTranslate($alert['MissionInfo']['missionReward']['items'][0]);
                }
                if (!is_null($_alert->items)) {
                    $this->pushNotification($rewardItem);
                }
                $_alert->stored_at = date("Y-m-d h:i:s");
                if ($_alert->save()) {
                    $count++;
                }
            }
        }
        $stop = microtime(true);
        $runtime = ($stop-$start)*1000;
        $avgRuntime = Cache::get('avgRuntime');
        $avgRuntime += $runtime;
        $avgRuntime /= 2;
        Cache::forever('avgRuntime', $avgRuntime);

        return response()->json(['status' => 'finished update',
                                 'new_alert' => $count, 'runtime' => $runtime.'ms']);
    }

    public function privateApi(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|alpha_num',
        ]);
        $alerts = array('stat' => 'BOOM SHAKA LAKA!');
        if ($request->input('key') === 'RFf6S64xhyj6GXQ5') {
            $alerts = Alert::where('expiry', '>', time())->get();
            foreach ($alerts as $alert) {
                $alert->description = trans('desc.'.$alert->description);
                $alert->location = trans('location.'.$alert->location);
                $alert->type = trans('types.'.$alert->type);
                $alert->faction = trans('types.'.$alert->faction);
                if (!is_null($alert->items)) {
                    $items = explode('|', $alert->items);
                    $newItems = array();
                    foreach ($items as $item) {
                        $item = explode(',', $item);
                        $itemTrans = trans('rewards.'.$item[0]);
                        if (!Lang::has('rewards.'.$item[0])) {
                            $t = Trans::where('item', $item[0])->first();
                            if ($t->trans != '') {
                                $itemTrans = $t->trans;
                            }
                        }
                        $item = array('item' => $itemTrans, 'count' => $item[1]);
                        array_push($newItems, $item);
                    }
                    $alert->items = $newItems;
                }
            }
            $usage = Cache::get('apiUsage');
            $usage++;
            Cache::forever('apiUsage', $usage);
            //Cache::increment('apiUsage');
        }
        //var_dump($alerts);
        return response()->json($alerts);
    }

    public function initCache()
    {
        Cache::forever('avgRuntime', 0);
        Cache::forever('apiUsage', 0);
    }

    public function systemStat()
    {
        return response()->json(['Average Runtime' => Cache::get('avgRuntime').'ms','Api Usage' => Cache::get('apiUsage')]);
    }

    private function pushNotification($item = null)
    {
        $notify = '天诺战士，你有新的警报提醒！';
        if (!is_null($item)) {
            $isExistsTranslate = false;
            if (Lang::has('rewards.'.$item)) {
                $isExistsTranslate = true;
                $itemTrans = trans('rewards.'.$item);
            } else if (Trans::where('item', $item)->count() != 0) {
                $isExistsTranslate = true;
                $itemTrans = Trans::where('item', $item)->first();
                $itemTrans = $itemTrans->trans;
            }
            $reward = $isExistsTranslate?'已确认：'.$itemTrans:'奖励未知';
            $notify = '天诺战士，开门，有你的快递。'.$reward;
        }
        $client = new JPushClient('0005e8ec6b081598918f30b3', '90c239abf92d7317b5bfe0fb');
        $result = $client->push()
                        ->setPlatform(M\all)
                        ->setAudience(M\all)
                        ->setNotification(M\notification($notify,
                                M\ios($notify, null, '+1')
                            ))
                        ->setOptions(M\options(null,null,null,true,null))
                        ->send();
    }

    private function validateTranslate($item)
    {
        if (!Lang::has('rewards.'.$item) && (Trans::where('item', $item)->count() == 0)) {
            $trans = new Trans();
            $trans->item = $item;
            $trans->save();
        }
    }

}

