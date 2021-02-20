<?php

namespace App\Http\Controllers;

use App\Verification;
use App\WorkerPhone;
use App\User;
use App\Role;

use Cookie;
use Illuminate\Http\Request;
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
        $phone = session('phone');
        $client = new \GuzzleHttp\Client();
        
        $response = $client->request(
            'GET',
            "https://smsc.ru/sys/send.php?" .
            "login=$this->login&psw=$this->password&phones=$phone&mes=code&call=1&fmt=3"
        );
        
        if ($response->getStatusCode() == 200) {
            $jsonBody = $response->getBody();
            $a = json_decode($jsonBody, true);

            //return response([$response->getBody()], 400);
            session([
              'server_ver_ID' => $a['id'],
              'server_ver_CODE' => substr($a['code'], -4),
              'server_ver_CNT' => $a['cnt']
            ]);

            return response()->json([
                'session' => session()->all()
            ]);
        }
        else {
            return response()->json([
                'error' => true,
                'error_msg' => 'Сбой в системе отправки кода'
            ]);
        }
    }

    public function verify(Request $request) {
        $request->validate(
          [
              //'title' => 'bail|required|unique:posts|max:255',
              'user_ver_code' => 'min:4|max:4',
          ],
          [
              'user_ver_code.min'     => Lang::get('auth.min_name'),
              'user_ver_code.max'     => Lang::get('auth.max_name'),
          ]
        );
        $userVerCode = $request['user_ver_code'];

        if ($userVerCode === session('server_ver_CODE']) {
          $this->traceString('server_ver_CODE: ' . session('server_ver_CODE'));
          $this->traceString('server_ver_ID: ' . session('server_ver_ID'));
          $this->traceString('user_ver_NUM: ' . session('user_ver_NUM'));
          $this->traceString('server_ver_CNT: ' . session('server_ver_CNT'));

          $status = 200;

          $actions = sesssion('actions');
          foreach($actions as $action) {
            switch($action) {
              case 'createWorkerAndWorkerPhone':
                $worker = new Worker();
                $worker->name = session('name');
                $worker->created_at = Carbon::now();
                $worker->updated_at = Carbon::now();
                $worker->save();

                if (!isset(session('workerPhone'))) {
                  $workerPhone = new WorkerPhone();
                  $workerPhone->number = session('phone');
                }
                else {
                  $workerPhone = session('workerPhone');
                }
                $workerPhone->worker_id = $worker->id;
                $workerPhone->type = WorkerPhone::PHONE_TYPE_CALL;
                $workerPhone->created_at = Carbon::now();
                $workerPhone->updated_at = Carbon::now();
                $workerPhone->save();
                break;
              case 'createUserAndBindWithWorker':
                $user = new User();
                $user->role = Role::WORKER_ID;
                $user->fullname = session('name');
                $user->name = session('name');
                $user->phone_call = session('phone');
                $user->password = session('password');
                $user->verified_phone = 1;
                $user->save();

                if (!isset($worker)) {
                  $worker = session('worker');
                }
                $worker->user_id = $user->id;
                $worker->save();
                break;
            }
          }
          Session::forget('name');
          Session::forget('phone');
          Session::forget('password');
          Session::forget('worker');
          Session::forget('workerPhone');

          session([
            'user' => $user,
            'user_is_logged_in' => true
          ]);
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
