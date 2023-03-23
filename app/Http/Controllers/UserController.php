<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    // get all users who have won the game, ordered by their score
    public function get() : JsonResponse
    {
        $users = User::where('won', 1)->get();
        $orderedUsers = $users->sortBy(function ($user) {
            return $user->seconds_to_beat * $user->guesses;
        });
        return response()->json(['users' => $orderedUsers]);
    }

    // Check if the user is signed in + sets the game data
    public function getSession()
    {
        session_start();
        if (isset($_SESSION['signed_in'])) {
            $signed_in = $_SESSION['signed_in'];
        } else {
            $signed_in = false;
        }

        $userData = $_SESSION['user'] ?? null;
        return response()->json(['signed_in' => $signed_in, 'userData' => $userData]);
    }

    // function for clearing the entire session
    public function clearSession()
    {
        session_start();
        session_unset();
        session_destroy();

        return response()->json('Session cleared');
    }

    // sets the user in the database
    public function set(Request $request) : JsonResponse
    {
        $user = new User();
        $user->name = $request->name;
        $user->minimum = $request->minNumber;
        $user->maximum = $request->maxNumber;
        $user->tries = $request->maxTries;
        $user->seconds = $request->maxSeconds;
        $user->save();

        session_start();
        $_SESSION['signed_in'] = true;
        $signed_in = $_SESSION['signed_in'];
        $_SESSION['user'] = $user;
        $_SESSION['answer'] = rand($request->minNumber, $request->maxNumber);
        session_write_close();

        return response()->json(['signed_in' => $signed_in]);
    }

    // Checks the guessed number, sends lower/higher if not correct
    public function guess(Request $request)
    {
        session_start();
        if ($_SESSION['answer'] == intval($request->answer)) {
            $user = $_SESSION['user'];
            $user->won = 1;
            return response()->json(['msg' => 'Correct']);
        } else {
            if ($_SESSION['answer'] < intval($request->answer)) {
                return response()->json(['msg' => 'Lower']);
            } else {
                return response()->json(['msg' => 'Higher']);
            }
        }
    }

    // Function for when the user has beaten the game
    public function sendGuess(Request $request)
    {
        session_start();
        $user = $_SESSION['user'];
        $user->guesses = $request->guesses;
        $user->seconds_to_beat = $request->seconds;
        $user->answer = $_SESSION['answer'];
        $user->save();
        return response()->json(['Score has been saved']);
    }
}