<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Alert, App\Invasion, App\Trans;
use Lang, RedisX;
use Carbon\Carbon;

class InterfacesController extends Controller
{

    public function alerts(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|alpha_num',
            'latestUpdate' => 'required|numeric',
        ]);
        $alerts = array('stat' => 'BOOM SHAKA LAKA!');
        if ($request->input('key') === 'RFf6S64xhyj6GXQ5') {
            $alerts = Alert::where('stored_at', '>', $request->input('latestUpdate'))->where('expiry', '>', time())->get();
            foreach ($alerts as $alert) {
                $alert->description = trans('desc.'.$alert->description);
                $alert->location = trans('location.'.$alert->location);
                $alert->type = trans('types.'.$alert->type);
                $alert->faction = trans('types.'.$alert->faction);
                $alert->items = $this->rewardFormator($alert->items);
            }
            RedisX::incr('alertsAPI');
        }
        return response()->json($alerts);
    }

    public function invasions(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|alpha_num',
            'latestComplete' => 'required|numeric',
        ]);
        $invasions = array('stat' => 'BOOM SHAKA LAKA!');
        if ($request->input('key') === 'RFf6S64xhyj6GXQ5') {
            $invasions = Invasion::where('stored_at', '>', $request->input('latestComplete'))->get();
            foreach ($invasions as $invasion) {
                $invasion->location = trans('location.'.$invasion->location);
                $invasion->attacker = $invasion->attacker;
                $invasion->attackerReward = $this->rewardFormator($invasion->attackerReward);
                $invasion->defender = $invasion->defender;
                $invasion->defenderReward = $this->rewardFormator($invasion->defenderReward);
            }
            RedisX::incr('invasionsAPI');
        }
        return response()->json($invasions);
    }

    public function rewards(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|alpha_num',
        ]);
        $rewards = array('stat' => 'BOOM SHAKA LAKA!');
        if ($request->input('key') === 'RFf6S64xhyj6GXQ5') {
            $result = Trans::all();
            $rewards = array();
            foreach ($result as $reward) {
                $temp = array('id' => $reward->id, 'reward' => $reward->trans);
                array_push($rewards, $temp);
            }
        }
        return response()->json($rewards);
    }

    public function alertsHistory(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|alpha_num',
        ]);
        $alerts = array('stat' => 'BOOM SHAKA LAKA!');
        if ($request->input('key') === 'RFf6S64xhyj6GXQ5') {
            $alerts = Alert::where('activation', '>', Carbon::today()->timestamp)->where('expiry', '<', time())->get();
            foreach ($alerts as $alert) {
                $alert->description = trans('desc.'.$alert->description);
                $alert->location = trans('location.'.$alert->location);
                $alert->type = trans('types.'.$alert->type);
                $alert->faction = trans('types.'.$alert->faction);
                $alert->items = $this->rewardFormator($alert->items);
            }
        }
        return response()->json($alerts);
    }

    public function invasionsHistory(Request $request)
    {
        $this->validate($request, [
            'key' => 'required|alpha_num',
        ]);
        $invasions = array('stat' => 'BOOM SHAKA LAKA!');
        if ($request->input('key') === 'RFf6S64xhyj6GXQ5') {
            $invasions = Invasion::where('activation', '>', Carbon::today()->timestamp)->where('completed', true)->get();
            foreach ($invasions as $invasion) {
                $invasion->location = trans('location.'.$invasion->location);
                $invasion->attacker = trans('types.'.$invasion->attacker);
                $invasion->attackerReward = $this->rewardFormator($invasion->attackerReward);
                $invasion->defender = trans('types.'.$invasion->defender);
                $invasion->defenderReward = $this->rewardFormator($invasion->defenderReward);
            }
        }
        return response()->json($invasions);
    }

    public function systemStat()
    {
        return response()->json(['alertsAPIUsage' => RedisX::get('alertsAPI'),
            'invasionsAPIUsage' => RedisX::get('invasionsAPI')]);
    }

    private function rewardFormator($item)
    {
        if (!is_null($item)) {
            $item = explode(',', $item);
            $itemTrans = trans('rewards.'.$item[0]);
            if (!Lang::has('rewards.'.$item[0])) {
                $t = Trans::where('item', $item[0])->first();
                if ($t->trans != '') {
                    $itemTrans = $t->trans;
                }
            }
            $item = array('item' => $itemTrans, 'count' => $item[1]);
        }
        return $item;
    }

}


