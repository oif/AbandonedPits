<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Trans;

class TransController extends Controller
{

    public function keng()
    {
        header("Content-Type: text/html; charset=gb2312");
        $start = microtime(true);
        $url='http://xyfw.xujc.com/login/index.php?c=Login&a=login';
        $kengdie = array('username' => 'swe13007', 'password' => 'lovesunny77', 'user_lb' => '%D1%A7%C9%FA');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded; charset=gb2312"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);    //不获取header
        curl_setopt($curl, CURLOPT_NOBODY, 0);    //只获取body
        curl_setopt($curl,CURLOPT_POST,count($kengdie));
        curl_setopt($curl,CURLOPT_POSTFIELDS,$kengdie);
        $content = curl_exec($curl);
        curl_close($curl);
        $result = strpos($content, '何兆银');
        // Analyze alerts and invasions
        echo "<pre>";
        echo $content;
        echo "</pre>";
        echo $result;
        // Analysis end
        $stop = microtime(true);
        $runtime = ($stop-$start)*1000;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Trans::where('trans', '')->get();
        //$items = Trans::all();
        return view('trans')->withItems($items);
    }

    public function show()
    {
        $items = Trans::all();
        return view('trans')->withItems($items);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $item = Trans::find($request->input('_id'));
        $item->trans = $request->input('trans');
        $item->save();
        return Redirect::to('/trans');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
