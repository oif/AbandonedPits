<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return phpinfo();
});

$app->get('profile/{id}', 'UsersController@profile');   // 用户 Profile
$app->get('profileUC/{id}', 'UsersController@profileUC');   // 非缓存用户 Profile

// 用户关系
$app->group(['prefix' => 'ship'], function () use ($app) {
    $app->get('randfo/{id}', 'App\Http\Controllers\UsersController@randfo');    // 随机关注

    $app->post('follow', 'App\Http\Controllers\UsersController@follow');    // 关注
    $app->post('unfollow', 'App\Http\Controllers\UsersController@unfollow');    // 取消关注

    $app->get('following/{id}', 'App\Http\Controllers\UsersController@following');  // 正在关注
    $app->get('follower/{id}', 'App\Http\Controllers\UsersController@follower');  // 关注者
});

$app->post('publish', 'StatsController@publish');   // 发布状态
$app->get('remove/{id}', 'StatsController@remove'); // 删除状态

$app->get('timeline', 'StatsController@all');   // 总时间轴
$app->get('timelineUC', 'StatsController@allUC');   // 非缓存总时间轴
$app->get('timeline/{id}', 'StatsController@timeline'); // 个人时间轴
$app->get('timelineUC/{id}', 'StatsController@timelineUC'); // 非缓存个人时间轴


$app->group(['prefix' => 'killer'], function () use ($app) {
    $app->get('timeline', 'App\Http\Controllers\StatsController@expireAllTimeline');  // 清空所有 Timeline 缓存
    $app->get('stat', 'App\Http\Controllers\StatsController@expireAllStat'); // 清空所有 Stat 缓存
});
