<?php

namespace App\Http\Controllers;

use Cookie;
use Illuminate\Http\Request;
use Response;

class NexmoVer extends Controller
{
    public function send(Request $request)
    {
        $cookie3 = $_COOKIE['verNUM'];
        $basic = new \Nexmo\Client\Credentials\Basic('8305b3ba', 'gbvu1fwMZ4IDv9IR');
        $client = new \Nexmo\Client(new \Nexmo\Client\Credentials\Container($basic));
        $verification = new \Nexmo\Verify\Verification($cookie3, 'Brand');
        $client->verify()->start($verification);
        echo "Started verification, `request_id` is " . $verification->getRequestId();
    }

    public function verify(Request $request) {
        $cookie1 = $_COOKIE['verCODE'];
        $cookie2 = $_COOKIE['verID'];

        $basic = new \Nexmo\Client\Credentials\Basic('8305b3ba', 'gbvu1fwMZ4IDv9IR');
        $client = new \Nexmo\Client(new \Nexmo\Client\Credentials\Container($basic));

        $verification = new \Nexmo\Verify\Verification($cookie2);
        $result = $client->verify()->check($verification, $cookie1);

        $value = $request->cookie('name');
        return $value;
    }
}
