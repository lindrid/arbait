<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class InnPassportVer extends Controller
{
    public function verify(Request $request)
    {
        $telNumber = $_COOKIE['user_ver_NUM'];
        $client = new \GuzzleHttp\Client();
        $response = $client->request(
            'GET',
            "https://smsc.ru/sys/send.php?" .
            "login=$this->login&psw=$this->password&phones=$telNumber&mes=code&call=1&fmt=3"
        );
        if ($response->getStatusCode() == 200) {
            $jsonBody = $response->getBody();
            $a = json_decode($jsonBody, true);

            return response()->json([
                'server_ver_ID' => $a['id'],
                'server_ver_CODE' => $a['code'],
                'server_ver_CNT' => $a['cnt'],
                'error' => false
            ]);
        }
        else {
            return response()->json([
                'error' => true,
                'error_msg' => 'Сбой в системе отправки кода'
            ]);
        }
    }
}
