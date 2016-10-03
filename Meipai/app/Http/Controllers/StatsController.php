<?php

namespace App\Http\Controllers;

use App\Stat;
use App\User;
use DB;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function publish(Request $request)
    {
        if ($request->isMethod('post')) {
            $userId = $request->input('id');
            $content = $request->input('content');
            if ($user = User::findOrFail($userId)) {
                if ($user->publishQueue($content)) {
                    return response()->json(array('stat' => 'publish success', 'response_time' => $this->getExcTime()));
                }
            }
        }

        return response()->json(array('stat' => 'publish fail', 'response_time' => $this->getExcTime()));
    }

    public function all()
    {
        $total = Stat::count();
        if (count($total) != 0) {
            $all = Stat::getAll($total);
        } else {
            $all = array();
        }
        //return response()->json(array('total' => $total, 'response_time' => $this->getExcTime()));
        return response()->json(array('total' => $total, 'response_time' => $this->getExcTime(), 'stats' => $all));
    }

    public function allUC()
    {
        $all = Stat::all();
        $total = count($all);
        return response()->json(array('total' => $total, 'response_time' => $this->getExcTime(), 'stats' => "SMP get $total, but not display"));
    }

    public function remove($id)
    {
        Stat::removeStat($id);

        return response()->json(array('stat' => 'removed', 'response_time' => $this->getExcTime()));
    }

    public function timeline($id, $offset = 0)
    {
        $statsID = Stat::getStatIDList($id, $offset);
        if (count($statsID) != 0) {
            $stats = Stat::getBatchBy(array_slice($statsID, 20));
        } else {
            $stats = array();
        }
        return response()->json(array('total' => count($stats), 'response_time' => $this->getExcTime(), 'stats' => $stats));
    }

    public function timelineUC($id)
    {
        $timelineUsers = User::getFollowing($id);   // 从 Redis 获取关注列表
        $stats = DB::table('stats')->whereIn('user_id', $timelineUsers)->orderBy('id', 'desc')->take(20)->get();
        return response()->json(array('total' => count($stats), 'response_time' => $this->getExcTime(), 'stats' => $stats));
    }

    public function expireAllTimeline()
    {
        Stat::expireAllTimeline(User::count());

        return response()->json(array('response_time' => $this->getExcTime()));
    }

    public function expireAllStat()
    {
        Stat::expireAllStat(Stat::count());

        return response()->json(array('response_time' => $this->getExcTime()));
    }
}
