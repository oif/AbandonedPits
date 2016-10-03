<?php

namespace App;

use Illuminate\Support\Facades\Redis;
use DB;
use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'content',
    ];

    const CACHE = 'stat:';
    const STAT_CACHE_EXP = 43200;   // 12 小时
    const TIMELINE_CACHE_EXP = 14400;    // 4 小时

    const ID = 'id';
    const CONTENT = 'content';
    const PUBLISHED_AT = 'published_at';
    const PUBLISHED_BY = 'published_by';

    const TIMELINE = 'timeline:';

    public static function setStat($stat, $userName)
    {
        $tag = self::CACHE.$stat->id;

        Redis::pipeline(function ($pipe) use ($tag, $stat, $userName) {
            $pipe->hmset($tag, array(
                self::ID => $stat->id,
                self::CONTENT => $stat->content,
                self::PUBLISHED_BY => $userName,
                self::PUBLISHED_AT => $stat->created_at,
            ));
            $pipe->expire($tag, Stat::STAT_CACHE_EXP);    // 设置过期时间
        });

        return true;
    }

    public static function getStatById($id)
    {
        $stat = Redis::hgetall(self::CACHE.$id);

        if (!empty($stat)) { // 缓存
            $stat['id'] = $id;
        } else {    // 未缓存
            $stat = self::find($id);
            self::setStat($stat, User::findOrFail($stat->user_id)->name);
        }

        return $stat;
    }

    public static function getBatchBy($IDList)
    {
        $all = Redis::pipeline(function ($pipe) use ($IDList) {
            $all = array();
            foreach ($IDList as $id) {
                array_push($all, $pipe->hgetall(Stat::CACHE.$id));
            }

            return $all;
        });
        $total = count($IDList);
        $nameList = array();

        for ($i = 0; $i < $total; ++$i) {
            if (empty($all[$i])) {
                $all[$i] = self::find($IDList[$i]);
                $userId = $all[$i]->user_id;
                if (key_exists($userId, $nameList)) {
                    $all[$i]['created_by'] = $nameList[$userId];
                } else {
                    $all[$i]['created_by'] = User::findOrFail($userId)->name;
                    $nameList[$userId] = $all[$i]['created_by'];
                }
                self::setStat($all[$i], $all[$i]['created_by']);
            }
        }

        return $all;
    }

    public static function getAll($total)
    {
        $all = Redis::pipeline(function ($pipe) use ($total) {
            $all = array();
            for ($id = 1; $id <= $total; ++$id) {
                $all[] = $pipe->hgetall(Stat::CACHE.$id);
            }

            return $all;
        });

        array_unshift($all, array());
        $nameList = array();
        for ($id = 1; $id <= $total; ++$id) {
            if (empty($all[$id])) {
                $all[$id] = self::find($id);
                $userId = $all[$id]->user_id;
                if (key_exists($userId, $nameList)) {
                    $all[$id]['created_by'] = $nameList[$userId];
                } else {
                    $all[$id]['created_by'] = User::findOrFail($userId)->name;
                    $nameList[$userId] = $all[$id]['created_by'];
                }
                self::setStat($all[$id], $all[$id]['created_by']);
            }
        }
        array_shift($all);  // 移除占位
        return $all;
    }

    public static function removeStat($id)
    {
        Redis::expire(self::CACHE.$id, -1); // 清除缓存
        $stat = self::find($id);

        return $stat->delete();    // 从数据库删除
    }

    public static function getStatIDList($id, $offset = 0, $limit = 20)
    {
        $tag = self::TIMELINE.$id;
        if (Redis::llen($tag) == 0) {
            $timelineUsers = User::getFollowing($id);   // 从 Redis 获取关注列表
            $timelineUsers[] = $id;
            $result = DB::table('stats')->select('id')->whereIn('user_id', $timelineUsers)->orderBy('id', 'desc')->take(200)->get();
            $statsID = Redis::pipeline(function ($pipe) use ($result, $tag) {
                $statsID = array();
                foreach ($result as $stat) {
                    $statsID[] = $stat->id;
                    $pipe->rpush($tag, $stat->id);
                }
                $pipe->expire($tag, Stat::TIMELINE_CACHE_EXP);    // 设置过期时间
                return $statsID;
            });
            array_pop($statsID);
            $statsID = array_reverse($statsID);
        } else {
            $statsID = Redis::lrange($tag, $offset, $offset + $limit - 1);
        }

        return $statsID;
    }

    public static function pushStatToCache($follower, $statID)
    {
        Redis::pipeline(function ($pipe) use ($follower, $statID) {
            foreach ($follower as $per) {
                $tag = self::TIMELINE.$per;
                $pipe->rpop($tag);  // 删除最旧
                $pipe->lpushx($tag, $statID);
                $pipe->expire($tag, Stat::TIMELINE_CACHE_EXP);    // 设置过期时间
            }
        });

        return true;
    }

    public static function refreshTimelineOf($id)
    {
        $tag = self::TIMELINE.$id;
        if (Redis::llen($tag) != 0) {
            Redis::expire($tag, -1);    // 强制过期
            $timelineUsers = User::getFollowing($id);   // 获取关注列表
            $timelineUsers[] = $id; // 加上自己
            $result = DB::table('stats')->select('id')->whereIn('user_id', $timelineUsers)->orderBy('id', 'desc')->take(200)->get();
            Redis::pipeline(function ($pipe) use ($result, $tag) {
                foreach ($result as $stat) {
                    $pipe->rpush($tag, $stat->id);
                }
                $pipe->expire($tag, Stat::TIMELINE_CACHE_EXP);    // 设置过期时间
            });
        }

        return true;
    }

    public static function expireAllTimeline($lines)
    {
        Redis::pipeline(function ($pipe) use ($lines) {
            for ($i = 0; $i <= $lines; ++$i) {
                $pipe->expire(Stat::TIMELINE.$i, -1);
            }
        });
    }

    public static function expireAllStat($lines)
    {
        Redis::pipeline(function ($pipe) use ($lines) {
            for ($i = 0; $i <= $lines; ++$i) {
                $pipe->expire(Stat::CACHE.$i, -1);
            }
        });
    }
}
