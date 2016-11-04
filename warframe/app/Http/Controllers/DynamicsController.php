<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Alert, App\Invasion, App\Trans;
use Lang, RedisX;

// JPush API
use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class DynamicsController extends Controller
{
    public function dynamic()
    {
        $start = microtime(true);
        $url='http://content-zhb.warframe.com.cn/dynamic/worldState.php';
        set_time_limit(15);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);    //不获取header
        curl_setopt($curl, CURLOPT_NOBODY, 0);    //只获取body
        $content = curl_exec($curl);
        curl_close($curl);
        $dynamic = json_decode($content, TRUE);
        // Analyze alerts and invasions
        $alerts = $this->alertsDynamic($dynamic['Alerts']);
        $invasions = $this->invasionsDynamic($dynamic['Invasions']);
        // Analysis end
        $stop = microtime(true);
        $runtime = ($stop-$start)*1000;
        $avgRuntime = RedisX::get('avgRuntime');
        $avgRuntime += $runtime;
        $avgRuntime /= 2;
        RedisX::set('avgRuntime', $avgRuntime);
        return response()->json(['stat' => 'success', 'alerts' => $alerts, 'invasions' => $invasions]);
    }

    private function alertsDynamic($alerts)
    {
        $count = 0;
        if (count($alerts) > 0) {
            foreach ($alerts as $alert) {
                // Initialize
                if (Alert::where('id', $alert['_id']['$id'])->count() != 0) {
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
                /*
                if (isset($alert['MissionInfo']['missionReward']['countedItems'])) {
                    $items = '';
                    foreach ($alert['MissionInfo']['missionReward']['countedItems'] as $item) {
                        $items .= $item['ItemType'];
                        //$this->validateTranslate($item['ItemType']);
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
                    //$this->validateTranslate($alert['MissionInfo']['missionReward']['items'][0]);
                }
                if (!is_null($_alert->items)) {
                    //$this->pushNotification($rewardItem);
                }
                */
                $reward = $this->rewardFormator($alert['MissionInfo']['missionReward'], false);
                if (!is_null($reward['item'])) {
                    $_alert->items = $reward['item'].','.$reward['count'];
                    $this->pushNotification(true, $reward['item']);
                }
                $_alert->stored_at = time();
                if ($_alert->save()) {
                    $count++;
                }
            }
        }
        return array('newAlerts' => $count);
    }

    private function invasionsDynamic($invasions)
    {
        $newInvasions = 0;
        $completedInvasions = 0;
        $updatedInvasions = 0;
        foreach ($invasions as $invasion) {
            /*
            echo "<pre>";
            if ($invasion['Completed']) {
                echo "Completed<br>";
            } else {
                echo "Invasion ID: ".$invasion['_id']['$id'].", Loca: ".trans("location.".$invasion['Node'])."<br>";
                echo "Goal: ".$invasion['Goal'] . ", Count:".$invasion['Count']."<br>";
                echo "Invasion progress:".round(abs($invasion['Count']/$invasion['Goal']),3)."%<br/>";
                echo "Attacker: ".trans("types.".$invasion['AttackerMissionInfo']['faction'])."<br>";
                echo "Attacker Reward: ". $this->rewardFormator($invasion['AttackerReward'])['item']." x ".$this->rewardFormator($invasion['AttackerReward'])['count']."<br/>-------<br/>";
                echo "Defender: ".trans("types.".$invasion['DefenderMissionInfo']['faction'])."<br>";
                echo "Defender Reward: ". $this->rewardFormator($invasion['DefenderReward'])['item']." x ".$this->rewardFormator($invasion['DefenderReward'])['count']."<br/>";
                echo "Activation: ".$invasion['Activation']['sec'], ", Usec: ".$invasion['Activation']['usec'];
            }
            echo "</pre>";
            */
            $inv = Invasion::where('id', $invasion['_id']['$id'])->first();
            if (is_null($inv)) {
                $inv = new Invasion();
                $inv->id = $invasion['_id']['$id'];
                $inv->location = $invasion['Node'];
                $inv->progress = round(($invasion['Count']/$invasion['Goal']),3);
                $inv->attacker = $invasion['AttackerMissionInfo']['faction'];
                $reward = $this->rewardFormator($invasion['AttackerReward']);
                $inv->attackerReward = $reward['item'].','.$reward['count'];
                $inv->defender = $invasion['DefenderMissionInfo']['faction'];
                $reward = $this->rewardFormator($invasion['DefenderReward']);
                $inv->defenderReward = $reward['item'].','.$reward['count'];
                $inv->activation = $invasion['Activation']['sec'];
                $inv->usec = $invasion['Activation']['usec'];
                $inv->completed = $invasion['Completed'];
                $inv->stored_at = time();
                if ($inv->save()) {
                    //echo 'New invasion saved<br>';
                    // A invasion is saved
                    //$this->pushNotification();    // Invasion nofitication
                    $newInvasions++;
                }
            } elseif(!is_null($inv) && $inv->completed == false) {
                if ($invasion['Completed'] == true) {   // Update invasion stat
                    $inv->completed = true;
                    if ($inv->save()) {
                        //echo 'Invasion: '.$inv->id.'completed<br>';
                        // A invasion completed
                        $completedInvasions++;
                    }
                } else {    // Update invasion progress
                    $pro = round(($invasion['Count']/$invasion['Goal']), 3);
                    if (abs($pro) > abs($inv->progress)) {
                        $inv->progress = $pro;
                        if ($inv->save()) {
                            // A invasion progress updated
                            $updatedInvasions++;
                        }
                    }
                }
            }
        }
        return array('newInvasions' => $newInvasions, 'updatedInvasions' => $updatedInvasions, 'completedInvasions' => $completedInvasions);
    }

    private function rewardFormator($reward, $flag = true) // if flag is false means use for alert else for invasion
    {
        $item = null;
        $count = 1;
        if (isset($reward['credits']) && $flag) {
            $item = 'credits';
            $count = $reward['credits'];
        } elseif (isset($reward['items'])) {
            $item = $reward['items'][0];
            $this->validateTranslate($item);
        } elseif (isset($reward['countedItems'])) {
            $item = $reward['countedItems'][0]['ItemType'];
            $count = $reward['countedItems'][0]['ItemCount'];
            $this->validateTranslate($item);
        }
        return array('item' => $item, 'count' => $count);
    }

    private function validateTranslate($item)
    {
        if (!Lang::has('rewards.'.$item) && (Trans::where('item', $item)->count() == 0)) {
            $trans = new Trans();
            $trans->item = $item;
            $trans->save();
        }
    }

    private function pushNotification($type = false, $item = null)   // true => alert, false => invasion
    {
        $notify = $type?'天诺战士，你有新的警报提醒。':'天诺战士，你有新的入侵提醒。';
        $audience = array('all');
        if (!is_null($item)) {
            $isExistsTranslate = false;
            if (Lang::has('rewards.'.$item)) {
                $isExistsTranslate = true;
                $itemTrans = trans('rewards.'.$item);
            } elseif (Trans::where('item', $item)->count() != 0) {
                $isExistsTranslate = true;
                $itemTrans = Trans::where('item', $item)->first();
                array_push($audience, $itemTrans->id);
                $itemTrans = $itemTrans->trans;
            }
            $reward = $isExistsTranslate?'已确认：'.$itemTrans:'奖励未知';
            $notify = $notify.$reward;
        }
        $client = new JPushClient('0005e8ec6b081598918f30b3', '90c239abf92d7317b5bfe0fb');
        $result = $client->push()
                        ->setPlatform(M\all)
                        ->setAudience(M\all)
                        //->setAudience(M\Audience(M\Tag($audience)))
                        ->setNotification(M\notification($notify,
                                M\ios($notify, 'sound.wav', '+1')
                            ))
                        ->setOptions(M\options(null,null,null,true,null))
                        ->send();
    }

}


