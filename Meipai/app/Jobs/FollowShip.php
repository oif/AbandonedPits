<?php

namespace App\Jobs;

use App\Stat;

class FollowShip extends Job
{
    private $flag;  // true => follow, false => unfollow
    private $self;
    private $target;

    public function __construct($user, $target, $flag)
    {
        $this->self = $user;
        $this->target = $target;
        $this->flag = $flag;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->flag) {   // Follow
            $this->self->follow($this->target);
        } else {    // Unfollow
            $this->self->unfollow($this->target);
        }
        //  无论是 follow 还是 unfo，都需要重整该用户已缓存的timeline
        Stat::refreshTimelineOf($this->self->id);
    }
}
