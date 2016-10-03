<?php

namespace App\Jobs;

use App\Stat;
use App\User;
use Queue;

class PublishStat extends Job
{

    private $stat;
    private $user;
    private $ppm = 100; // Push per mission

    public function __construct($user, $content)
    {
        $this->user = $user;
        $this->stat = array(
            'user_id' => $user->id,
            'content' => $content,
        );

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createStat();    // 创建状态到数据库中
        $this->cacheStat();
        $this->pushFlow();
    }

    private function createStat()
    {
        return $this->stat = Stat::create($this->stat);
    }

    private function cacheStat()
    {
        return Stat::setStat($this->stat, $this->user->name);
    }

    private function pushFlow()
    {
        $follower = User::getFollower($this->user->id);
        $times = ceil(count($follower) / $this->ppm);
        for ($i = 0; $i < $times; $i++) {
            Queue::push(new PushFlow($this->stat->id, array_slice($follower, $i * $this->ppm, $this->ppm)));
        }
        return true;
    }
}
