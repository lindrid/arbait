<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SpaController extends Controller
{
    public function index()
    {
        $userIsLoggedIn = false;
        $userName = '';
        $userIsLoggedIn = session('user_is_logged_in');
        if ($userIsLoggedIn)
        {
            $user = session('user');
            $userName = $user->name;
        }
        $route = \Request::get('route');
        $errorMsg = \Request::get('errorMsg');
        //$routeName = \Request::get('routeName');
        //$userPrivileges = \Request::get('privileges');;

        return view('spa', [
            'userIsLoggedIn' => $userIsLoggedIn,
            'userName' => $userName,
            'errorMsg' => $errorMsg,
            'route' => $route
        ]);
    }
}