<?php

/*
 * Created by Neo on 15/9/15.
 */

include_once('simple_html_dom.php');

class DinRepairBooking {
    private static $mainUrl = 'http://ebooking.applewf.com/ebooking.php';
    private static $timeSlotUrl = 'http://ebooking.applewf.com/ajax/get_timeslot.php';
    public $cookie;

    function getCookieJar()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::$mainUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);    //只获取header
        curl_setopt($curl, CURLOPT_NOBODY, 1);    //不获取body
        $content = curl_exec($curl);
        curl_close($curl);
        list($header, $body) = explode("\r\n\r\n", $content);
        preg_match("/set\-cookie:([^\r\n]*)/i", $header, $matches);
        return $matches[1];
    }

    function getTimeSlot($date)
    {
        $fields=array(
            'c' => 3,
            'date' => $date,
            'qty' => 1,
            'prod' => '78',
            'time' => time()
        );
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, self::$timeSlotUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);    //不获取header
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //获取body
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$fields);
        ob_start();
        curl_exec($ch);
        $result=ob_get_contents();
        ob_end_clean();
        curl_close($ch);
        return $result;
    }
}

$x = new DinRepairBooking();
$dom = new simple_html_dom();
$x->cookie = $x->getCookieJar();
for ($i=0; $i < 10; $i++) {

    $date = date('Y-m-d', strtotime($i.'day'));
    $data = $x->getTimeSlot($date);

    $dom->load($data);
    $schedule = $dom->find('option');
    array_shift($schedule);

    echo "Date: $date <br><pre>";
    if (count($schedule) == 0) {
        echo "Empty<br>";
    }
    foreach($schedule as $element)
       echo $element->innertext . '<br>';
    echo "</pre>";
}