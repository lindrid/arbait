<?php

namespace App\Http\Controllers;

use App\Verification;
use Cookie;
use Illuminate\Http\Request;
use JWTAuth;
use Response;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class SmscVer extends Controller
{
    private $login = 'arbait';
    private $password = 'X5AfnrPwLR9jUZ4';

    public function send(Request $request)
    {
        $this->attack($request);
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

            return response([$response->getBody()], 400);
            return response()->json([
                'server_ver_ID' => $a['id'],
                'server_ver_CODE' => substr($a['code'], -4),
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

    public function verify(Request $request)
    {
        $userVerCode = $request['user_ver_code'];

        $this->traceError($request);

        if ( isset($_COOKIE['server_ver_CODE']) &&
            ($userVerCode == $_COOKIE['server_ver_CODE']) )
        {
            $this->traceString('server_ver_CODE: ' . $_COOKIE['server_ver_CODE']);
            $this->traceString('server_ver_ID: ' . $_COOKIE['server_ver_ID']);
            $this->traceString('user_ver_NUM: ' . $_COOKIE['user_ver_NUM']);
            $this->traceString('server_ver_CNT: ' . $_COOKIE['server_ver_CNT']);

            $status = 200;
            /*$ver = new Verification;
            $ver->id = $_COOKIE['server_ver_ID'];
            $ver->code = $_COOKIE['server_ver_CODE'];
            $ver->tel_number = $_COOKIE['user_ver_NUM'];
            $ver->cnt = $_COOKIE['server_ver_CNT'];
            $ver->user_id = JWTAuth::parseToken()->authenticate()->id;
            $ver->save();*/

            $userId = JWTAuth::parseToken()->authenticate()->id;
            DB::table('users')
                ->where('id', $userId)
                ->update(array('verified_phone' => 1));

            (new CRegistrationProgress())->save($request);
        }
        else {
            $status = 400;
            $this->traceError($request);
        }

        return response([], $status);
    }

    public function attack(Request $request)
    {
        $_COOKIE['user_ver_NUM'] = $request['phone_num'];

    }

    private function traceString($s)
    {
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "=======================");
        $dateTime = Carbon::now()->toDayDateTimeString();
        Storage::append('error.txt', "$dateTime, Error in WorkerOccupationsAndSkillsController.store");
        Storage::append('error.txt', "=======================");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', $s);
    }

    private function traceError(Request $request)
    {
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "=======================");
        $dateTime = Carbon::now()->toDayDateTimeString();
        Storage::append('error.txt', "$dateTime, Error in WorkerOccupationsAndSkillsController.store");
        Storage::append('error.txt', "=======================");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', $request);
    }
}
