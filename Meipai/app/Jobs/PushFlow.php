<?php

namespace App\Jobs;

use App\Stat;

class PushFlow extends Job
{

    private $statID;
    private $followingSection;

    public function __construct($id, $following)
    {
        $this->statID = $id;
        $this->followingSection = $following;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Stat::pushStatToCache($this->followingSection, $this->statID);
    }
}
