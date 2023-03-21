<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function get(Request $request) : JsonResponse
    {
        if (session()->has('signed_in')) {
            $signed_in = session()->get('signed_in');
        } else {
            $signed_in = false;
        }
        $userData = session()->get('user');
        // session()->forget('signed_in');
        $users = User::where('won', 1)->get();
        $orderedUsers = $users->orderBy()
        return response()->json(['signed_in' => $signed_in, 'users' => $users, 'userData' => $userData]);
    }

    public function set(Request $request) : JsonResponse
    {
        $user = new User();
        $user->name = $request->name;
        $user->minimum = $request->minNumber;
        $user->maximum = $request->maxNumber;
        $user->tries = $request->maxTries;
        $user->seconds = $request->maxSeconds;
        $user->save();
        
        session()->put('signed_in', true);
        $signed_in = session()->get('signed_in');
        session()->put('user', $user);
        session()->put('answer', rand($request->minNumber, $request->maxNumber));
        session()->save();
        return response()->json(['signed_in' => $signed_in]);
    }

    public function guess(Request $request)
    {
        if (session()->get('answer') == intval($request->answer)) {
            $user = session()->get('user');
            $user->won = 1;
            return response()->json(['msg' => 'Correct']);
        } else {
            if (session()->get('answer') < intval($request->answer)) {
                return response()->json(['msg' => 'Lower']);
            } else {
                return response()->json(['msg' => 'Higher']);
            }

        }
    }

    public function sendGuess(Request $request)
    {
        $user = session()->get('user');
        $user->guesses = $request->guesses;
        $user->seconds_to_beat = $request->seconds;
        $user->answer = session()->get('answer');
        $user->save();
        return response()->json(['Score has been saved']);
    }
}