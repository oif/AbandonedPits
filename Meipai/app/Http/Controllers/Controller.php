<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    private $exctime = 0;

    public function __construct()
    {
        $this->startExc(true);
    }

    public function startExc($start = false)
    {
        if ($start) {
            $this->exctime = microtime(true);
            return;
        }
        return microtime(true) - $this->exctime;
    }

    public function getExcTime()
    {
        return number_format($this->startExc() * 1000, 5) . 'ms';
    }
}
