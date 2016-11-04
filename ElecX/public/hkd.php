<?php

/*
 * Created by Neo on 15/9/15.
 */

ini_set('date.timezone','Asia/Hongkong');

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
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_NOBODY, 1);
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
            'prod' => '100',
            'time' => time()
        );
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL, self::$timeSlotUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);    //涓嶈幏鍙杊eader
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //鑾峰彇body
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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title> Hong Kong Din-Din Apple Repair Schedule </title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <style type="text/css">
        .mobi1, .mobi2, .pad1, .pad2, .pad3, .pad4, .pad5, .pad6, .pc1, .pc2, .pc3,
    .pc4, .pc5, .pc6, .pc7, .pc8, .pc9, .pc10, .pc11, .pc12 {
        position: relative;
        min-height: 1px;
        padding-left: 15px;
        padding-right: 15px
    }

    .wrapper {
        margin-right: auto;
        margin-left: auto;
        padding-left: 15px;
        padding-right: 15px
    }

    .wrapper:before, .wrapper:after {
        content: " ";
        display: table
    }

    .wrapper:after {
        clear: both
    }

    @media (min-width: 479px) {
        .wrapper {
            width: 470px
        }
    }

    @media (min-width: 767px) {
        .wrapper {
            width: 750px
        }
    }

    @media (min-width: 991px) {
        .wrapper {
            width: 750px
        }
    }

    .row {
        margin-left: -15px;
        margin-right: -15px
    }

    .row:before, .row:after {
        content: " ";
        display: table
    }

    .row:after {
        clear: both
    }

    .mobi1, .mobi2 {
        float: left
    }

    .mobi1 {
        width: 50%
    }

    .mobiml1 {
        margin-left: 50%
    }

    .mobi2 {
        width: 100%
    }

    .mobiml2 {
        margin-left: 100%
    }

    @media screen and (min-width: 767px) {
        .pad1, .pad2, .pad3, .pad4, .pad5, .pad6 {
            float: left
        }

        .pad1 {
            width: 16.66667%
        }

        .padml1 {
            margin-left: 16.66667%
        }

        .pad2 {
            width: 33.33333%
        }

        .padml2 {
            margin-left: 33.33333%
        }

        .pad3 {
            width: 50%
        }

        .padml3 {
            margin-left: 50%
        }

        .pad4 {
            width: 66.66667%
        }

        .padml4 {
            margin-left: 66.66667%
        }

        .pad5 {
            width: 83.33333%
        }

        .padml5 {
            margin-left: 83.33333%
        }

        .pad6 {
            width: 100%
        }

        .padml6 {
            margin-left: 100%
        }
    }

    @media screen and (min-width: 991px) {
        .pc1, .pc2, .pc3, .pc4, .pc5, .pc6, .pc7, .pc8, .pc9, .pc10, .pc11, .pc12 {
            float: left
        }

        .pc1 {
            width: 8.33333%
        }

        .pcml1 {
            margin-left: 8.33333%
        }

        .pc2 {
            width: 16.66667%
        }

        .pcml2 {
            margin-left: 16.66667%
        }

        .pc3 {
            width: 25%
        }

        .pcml3 {
            margin-left: 25%
        }

        .pc4 {
            width: 33.33333%
        }

        .pcml4 {
            margin-left: 33.33333%
        }

        .pc5 {
            width: 41.66667%
        }

        .pcml5 {
            margin-left: 41.66667%
        }

        .pc6 {
            width: 50%
        }

        .pcml6 {
            margin-left: 50%
        }

        .pc7 {
            width: 58.33333%
        }

        .pcml7 {
            margin-left: 58.33333%
        }

        .pc8 {
            width: 66.66667%
        }

        .pcml8 {
            margin-left: 66.66667%
        }

        .pc9 {
            width: 75%
        }

        .pcml9 {
            margin-left: 75%
        }

        .pc10 {
            width: 83.33333%
        }

        .pcml10 {
            margin-left: 83.33333%
        }

        .pc11 {
            width: 91.66667%
        }

        .pcml11 {
            margin-left: 91.66667%
        }

        .pc12 {
            width: 100%
        }

        .pcml12 {
            margin-left: 100%
        }
    }

    * {
        -webkit-font-smoothing: antialiased
    }

    *, *:before, *:after {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box
    }

    body {
        position: relative;
        color: #404040;
        background-color: #fff;
        font-weight: 100;
        -webkit-font-smoothing: antialiased;
        background-origin: border-box;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box
    }

    body, h1, h2, h3, h4, h5, h6, hr, p, blockquote, dl, dt, dd, ul, ol, li, pre,
    fieldset, lengend, button, input, textarea, th, td {
        margin: 0;
        padding: 0
    }

    body, button, input, select, textarea {
        font: 14px/2 AvenirNext-Regular, "proxima-nova", "Hiragino Sans GB", "Microsoft YaHei", "WenQuanYi Micro Hei", "Open Sans", "Helvetica Neue", Arial, sans-serif
    }

    h1 {
        font-size: 36px
    }

    h2 {
        font-size: 24px
    }

    h3 {
        font-size: 14px
    }

    h4, h5, h6 {
        font-size: 100%
    }

    address, cite, dfn, em, var {
        font-style: normal
    }

    code, kbd, pre, samp, tt {
        font-family: "Courier New", Courier, monospace
    }

    small {
        font-size: 12px
    }

    ul, ol {
        list-style: none
    }

    abbr[title], acronym[title] {
        border-bottom: 1px dotted;
        cursor: help
    }

    q:before, q:after {
        content: ''
    }

    legend {
        color: #000
    }

    button, input, select, textarea {
        font-size: 100%
    }

    table {
        border-collapse: collapse;
        border-spacing: 0
    }

    hr {
        border: none;
        height: 1px
    }

    p {
        padding: 14px 0
    }

    img {
        vertical-align: middle;
        max-width: 100% !important;
        border: 0;
        page-break-inside: avoid
    }

    a {
        background: transparent;
        text-decoration: none;
        color: #404040
    }

    .close {
        float: right;
        font-size: 28px;
        font-weight: normal;
        line-height: 1;
        color: #404040;
        text-shadow: 0 1px 0 #fff;
        opacity: 0.3;
        filter: alpha(opacity=30)
    }

    .close:hover, .close:focus {
        color: #404040;
        text-decoration: none;
        cursor: pointer;
        opacity: 0.6;
        filter: alpha(opacity=60)
    }

    button.close {
        padding: 0;
        cursor: pointer;
        background: transparent;
        border: 0;
        -webkit-appearance: none
    }

    .popopen {
        overflow: hidden;
        position: relative
    }

    .pop {
        display: none;
        overflow: hidden;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 9999;
        -webkit-overflow-scrolling: touch;
        outline: 0
    }

    .pop.in .popwrap {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0)
    }

    .popopen .pop {
        overflow-x: hidden;
        overflow-y: auto
    }

    .popwrap {
        position: relative;
        width: auto;
        margin: 10px;
        -webkit-transform: translate3d(0, -25%, 0);
        transform: translate3d(0, -25%, 0);
        -webkit-transition: -webkit-transform 0.3s ease-out;
        -moz-transition: -moz-transform 0.3s ease-out;
        -o-transition: -o-transform 0.3s ease-out;
        transition: transform 0.3s ease-out
    }

    .popbox {
        position: relative;
        background-color: #fff;
        border: 1px solid #dedede;
        border-radius: 3px;
        -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
        box-shadow: 0 3px 9px rgba(0, 0, 0, 0.5);
        background-clip: padding-box;
        outline: 0
    }

    .popmask {
        display: none;
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: #000;
        opacity: 0.3;
        filter: alpha(opacity=30)
    }

    .popheader {
        padding: 7px;
        border-bottom: 1px solid #f7f7f7;
        min-height: 14px
    }

    .poptitle {
        margin: 0;
        text-align: center;
        line-height: 2
    }

    .popbody {
        position: relative;
        padding: 14px
    }

    .popfooter {
        padding: 14px;
        text-align: right;
        border-top: 1px solid #f7f7f7
    }

    .popfooter:before, .popfooter:after {
        content: " ";
        display: table
    }

    .popfooter:after {
        clear: both
    }

    .popfooter .btn+.btn {
        margin-left: 5px;
        margin-bottom: 0
    }

    .popfooter .btn-group .btn+.btn {
        margin-left: -1px
    }

    .popfooter .btn-block+.btn-block {
        margin-left: 0
    }

    .modal-scrollbar-measure {
        position: absolute;
        top: -9999px;
        width: 50px;
        height: 50px;
        overflow: scroll
    }

    @media (min-width: 767px) {
        .popwrap {
            width: 600px;
            margin: 100px auto
        }

        .popbox {
            -webkit-box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5)
        }

        .popsm {
            width: 300px
        }
    }

    @media (min-width: 991px) {
        .poplg {
            width: 900px
        }
    }

    fieldset {
        padding: 0;
        margin: 0;
        border: 0;
        min-width: 0
    }

    legend {
        display: block;
        width: 100%;
        padding: 0;
        margin-bottom: 2;
        font-size: 21px;
        line-height: inherit;
        color: #404040;
        border: 0;
        border-bottom: 1px solid #404040
    }

    .wrapper h2 {
        margin-top: 84px;
        margin-bottom: 14px
    }

    .wrapper dt {
        font-weight: bold
    }

    .activityGuide {
        text-align: center;
        margin-top: 14px;
        margin-bottom: 112px
    }

    .activityGuide .typewriter {
        width: 200px;
        height: 219px;
        margin: 0 auto;
        background: #fff  no-repeat center center
    }

    @media (-moz-min-device-pixel-ratio: 1.3), (-o-min-device-pixel-ratio: 2.6 / 2), (-webkit-min-device-pixel-ratio: 1.3), (min-device-pixel-ratio: 1.3), (min-resolution: 1.3dppx) {
        .activityGuide .typewriter {
            background-size: 200px 219px
        }
    }

    .activityGuide h1 {
        font-weight: 100;
        font-size: 28px;
        line-height: 1em;
        padding: 56px 0 56px
    }

    .activityGuide p {
        padding-top: 7px;
        padding-bottom: 0
    }

    .activityGuide .countDown {
        line-height: 32px
    }

    .activityGuide .countDown em {
        font-size: 32px;
        padding-left: 14px
    }

    .activityGuide .countDown em:first-child {
        padding-left: 0
    }

    .gameForm dl {
        margin-bottom: 28px
    }

    .gameForm dl dt {
        margin-top: 28px;
        margin-bottom: 14px
    }

    .gameForm p a {
        border-bottom: 1px solid #bfbfbf
    }

    .enroll {
        margin-bottom: 112px
    }

    .enroll h2 {
        text-align: center
    }

    .enroll .enrollWrap {
        width: 300px;
        margin: auto
    }

    .enroll .enrollWrap .form-group {
        margin-top: 14px
    }

    .assessment dl {
        padding-top: 7px
    }

    .assessment dl dt {
        margin-top: 14px;
        margin-bottom: 14px
    }

    .instructions a {
        border-bottom: 1px solid #bfbfbf
    }

    .prizes dd {
        margin-bottom: 56px
    }

    .judges ul li {
        margin-top: 28px
    }

    .judges ul li img {
        display: block;
        float: left;
        border-radius: 50%
    }

    .judges ul li .judgeDetail {
        margin-left: 125px
    }

    .judges ul li .judgeDetail h4 {
        padding-top: 14px
    }

    .schedule dl dd {
        margin-bottom: 14px
    }

    .partners dl dd a {
        display: inline-block;
        margin-right: 14px;
        margin-top: 7px;
        margin-bottom: 28px
    }

    .partners dl dd a img {
        vertical-align: middle
    }

    .partners dl dd a.fir {
        margin-left: 28px
    }

    .activesign {
        margin: 112px auto
    }

    .activesign .btn {
        padding: 14px 28px
    }

    .indexFooter {
        background-color: #404040;
        padding-top: 84px;
        padding-bottom: 84px
    }

    .indexFooter .wrapper {
        color: #fff
    }

    .indexFooter .wrapper small {
        font-size: 10px;
        display: block;
        color: #bfbfbf
    }

    .indexFooter .wrapper small a {
        color: #bfbfbf
    }

    .indexFooter .wrapper small a:hover {
        text-decoration: underline
    }

    .goEnrollWrap {
        position: fixed;
        bottom: 0;
        z-index: 9;
        width: 100%
    }

    .goEnrollWrap .wrapper {
        position: relative;
        bottom: 150px
    }

    .goEnrollWrap .wrapper .goEnroll {
        cursor: pointer;
        padding: 23px 15px;
        border-radius: 100%;
        position: absolute;
        top: 0;
        right: -55px;
        font-size: 16px;
        line-height: 1;
        color: #fff;
        background-color: #404040
    }

    .goEnrollWrap .wrapper .goEnroll:hover {
        background-color: #000
    }

    @media screen and (max-width: 991px) {
        .indexFooter {
            text-align: center;
            padding-top: 28px;
            padding-bottom: 28px
        }

        .goEnrollWrap .wrapper .goEnroll {
            right: 20px
        }
    }

    @media screen and (max-width: 767px) {
        .activityGuide h1 {
            font-size: 22px;
            line-height: 2.4
        }

        .enroll .enrollWrap {
            width: 100%
        }

        .partners dl dd {
            text-align: center
        }

        .partners dl dd a {
            display: block
        }

        .partners dl dd a.fir {
            margin-left: 0
        }

        .activesign {
            text-align: center
        }

        .goEnrollWrap {
            position: fixed;
            bottom: 0;
            z-index: 9;
            width: 100%;
            overflow: visible
        }

        .goEnrollWrap .wrapper {
            height: 1px;
            bottom: 165px
        }

        .goEnrollWrap .wrapper .goEnroll {
            right: 7px;
            top: 0;
            padding: 23px 15px;
            border-right: none
        }

    }
    </style>
</head>
<body>
<div class="activityGuide wrapper">
    <div class="typewriter"></div>
    <h1><span style="font-size:57px;">Fo Tan</span></h1>
</div>
<div class="wrapper">
    <p>TODO: Auto booking.</p>
</div>
<div class="gameForm wrapper">
    <h2>Schedule</h2>
    <p>Recently update: <?=date("Y-m-d h:i:sa")?></p>
    <?php
        $x = new DinRepairBooking();
        $dom = new simple_html_dom();
        $x->cookie = $x->getCookieJar();
        for ($i=0; $i < 10; $i++) {

            $date = date('m-d', strtotime($i.'day'));
            $data = $x->getTimeSlot($date);

            $dom->load($data);
            $schedule = $dom->find('option');
            array_shift($schedule);
            if (count($schedule) != 0) {
    ?>
        <div style="margin-top:20px;font-size:27px"><?=$date?></div>
        <div class="row">
            <?php
                /*
                if (count($schedule) == 0) {
                    echo "<div class=\"pc3\">Empty <img src=\"https://www.v2ex.com/static/img/doge.gif\" style=\"vertical-align: middle; margin: 0px 0px 2px 0px\" alt=\"doge\"></div>";
                } else {
                */
                    foreach($schedule as $element) {
                        echo "<div class=\"pc3\">";
                        echo '<strong>' . $element->innertext . '</strong>';
                        echo "</div>";
                    }
            echo "</div>";
            }
        }
    ?>
</div>
<div id="enroll" class="enroll wrapper">
    <h2>Just for HK-D</h2>
    <div class="enrollWrap">
    <p style="text-align:center">Crafted by Neo, Amoy 2015.9.15</p>
    </div>
</div>
</body>
</html>