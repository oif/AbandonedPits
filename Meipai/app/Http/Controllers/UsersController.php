<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function profile($id)
    {
        $user = User::getUser($id);
        $user['id'] = $id;
        $user['response_time'] = $this->getExcTime();   // 响应时间
        return response()->json($user);
    }

    public function profileUC($id)
    {
        $user = User::findOrFail($id);
        $user['response_time'] = $this->getExcTime();   // 响应时间
        return response()->json($user);
    }

    public function follow(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = User::findOrFail($request->input('user_id'));
            $target = User::findOrFail($request->input('target_id'));
            if ($user && $target) {
                if ($user->followQueue($target)) {
                    return response()->json(
                        array(
                            'stat' => "following $target->name [ID: $target->id]",
                            'response_time' => $this->getExcTime()
                        )
                    );
                }
            }
        }
        return response()->json(
            array(
                'stat' => "unable to follow $target->name [ID: $target->id]",
                'response_time' => $this->getExcTime()
            )
        );
    }

    public function unfollow(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = User::findOrFail($request->input('user_id'));
            $target = User::findOrFail($request->input('target_id'));
            if ($user && $target) {
                if ($user->unfollowQueue($target)) {
                    return response()->json(
                        array(
                            'stat' => "unfollowed $target->name [ID: $target->id]",
                            'response_time' => $this->getExcTime()
                        )
                    );
                }
            }
        }
        return response()->json(
            array(
                'stat' => "unable to unfollow $target->name [ID: $target->id]",
                'response_time' => $this->getExcTime()
            )
        );
    }

    public function following($id)
    {
        return response()->json(
            array(
                'following' => User::getFollowing($id),
                'response_time' => $this->getExcTime()
            )
        );
    }

    public function follower($id)
    {
        return response()->json(
            array(
                'follower' => User::getFollower($id),
                'response_time' => $this->getExcTime()
            )
        );
    }

    public function randfo($id)
    {
        $user = User::findOrFail($id);
        for ($i=0; $i < 50; $i++) {
            $target = User::findOrFail(rand(1, User::count()));
            if ($user) {
                $user->followQueue($target);
            }
        }
        return response()->json(
            array(
                'stat' => "following random people",
                'response_time' => $this->getExcTime()
            )
        );
    }

}
