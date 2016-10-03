<?php

namespace App;

use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use Queue;
use App\Jobs\FollowShip;
use App\Jobs\PublishStat;

class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    const USER_PROFILE_EXP = 720;   // 720秒 12分钟
    const USER_PROFILE_TAG = 'user:profile:';
    const USER_NAME = 'name';
    const USER_STATS = 'stats';
    const USER_FOLLOWER = 'follower';
    const USER_FOLLOWING = 'following';

    const FOLLOWING = 'user:following:';
    const FOLLOWER = 'user:follower:';

    private static function getProfileTag($id)
    {
        return self::USER_PROFILE_EXP.$id;
    }

    private static function getFollowingTag($id)
    {
        return self::FOLLOWING.$id;
    }

    private static function getFollowerTag($id)
    {
        return self::FOLLOWER.$id;
    }

    private function incrUserProfile($key)
    {
        // 更新缓存
        if (Redis::hincrby(self::getProfileTag($this->id), $key, 1)) {
            ++$this->$key;
            if ($this->save()) {
                return true;
            }
        }

        return false;
    }

    private function decrUserProfile($key)
    {
        // 更新缓存
        if (Redis::hincrby(self::getProfileTag($this->id), $key, -1)) {
            --$this->$key;
            if ($this->save()) {
                return true;
            }
        }

        return false;
    }

    public function follow($target)
    {
        $following = self::getFollowingTag($this->id);  // 自己的关注列表
        $follower = self::getFollowerTag($target->id); // 对方的关注者列表

        if (Redis::sadd($following, $target->id)) {   // 将对方加入自己的关注列表
            if (Redis::sadd($follower, $this->id)) {  // 将自己加入对方的关注者列表
                // 更新计数
                $this->incrUserProfile(self::USER_FOLLOWING);
                $target->incrUserProfile(self::USER_FOLLOWER);

                return true;
            } else {
                Redis::srem($following, $target->id);
            }
        }

        return false;
    }

    public function unfollow($target)
    {
        $following = self::getFollowingTag($this->id);  // 自己的关注列表
        $follower = self::getFollowerTag($target->id); // 对方的关注者列表

        if (Redis::srem($following, $target->id)) {   // 将对方从自己的关注列表移除
            if (Redis::srem($follower, $this->id)) {  // 将自己从对方的关注者列表移除
                // 更新计数
                $this->decrUserProfile(self::USER_FOLLOWING);
                $target->decrUserProfile(self::USER_FOLLOWER);

                return true;
            } else {
                Redis::sadd($following, $target->id);
            }
        }

        return false;
    }

    public static function getUser($id)
    {
        $tag = self::getProfileTag($id);

        $user = Redis::hgetall($tag);

        if (!key_exists('name', $user)) {
            $user = self::findOrFail($id);

            Redis::pipeline(function ($pipe) use ($tag, $user) {
                $pipe->hmset($tag, array(
                    self::USER_NAME => $user->name,
                    self::USER_STATS => $user->stats,
                    self::USER_FOLLOWING => $user->following,
                    self::USER_FOLLOWER => $user->follower,
                ));
                $pipe->expire($tag, User::USER_PROFILE_EXP);    // 设置过期时间
            });

            //unset($user['created_at']);
            //unset($user['updated_at']);
        } else {
            // 从缓存中取出
        }

        return $user;
    }

    public function publishQueue($content)
    {
        if (Queue::push(new PublishStat($this, $content))) {
            $this->incrUserProfile(self::USER_STATS);    // 已发布状态计数器
        }

        return true;
    }

    public function followQueue($target)
    {
        Queue::push(new FollowShip($this, $target, true));

        return true;
    }

    public function unfollowQueue($target)
    {
        Queue::push(new FollowShip($this, $target, false));

        return true;
    }

    public static function getFollowing($id)
    {
        return Redis::smembers(self::getFollowingTag($id));
    }

    public static function getFollower($id)
    {
        return Redis::smembers(self::getFollowerTag($id));
    }
}
