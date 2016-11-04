<?php

namespace ElecX\Http\Controllers;

use Illuminate\Http\Request;

use ElecX\Http\Requests;
use ElecX\Http\Controllers\Controller;

use ElecX\Dorm;
use Queue;

class MonitorController extends Controller
{

    public function workflowStart()
    {
        $dorms = Dorm::all();
        $count = 0;
        foreach ($dorms as $dorm) {
            Queue::push('ElecX\Commands\PullElec@fire', $dorm->id);
            $count++;
        }
        echo $count . " update";
    }

    public function notificationStart()
    {
        $dorms = Dorm::all();
        $count = 0;
        foreach ($dorms as $dorm) {
            if ($dorm->notificationSwitch && !$dorm->notificationSent) {
                Queue::push('ElecX\Commands\PushNotification@fire', $dorm->id);
                $count++;
            }
        }
        echo $count . " sent";
    }
}
