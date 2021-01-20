<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/*
 * постраничный прогресс регистрации или иного процесса.
 * дает возможность контроллировать на каком шаге находится юзер, 
 * присекать попытки перепрыгнуть через необходимую страницу
 */
class CRegistrationProgress extends Controller
{
    private $pages = array (
        'w' => [
            'personal-data'     => 1,
            'citizen'           => 2,
            'register'          => 3,
            'alien'             => 3,
            'verify'            => 4,
            'offer'             => 5,
            'rules'             => 6,
            'link'              => 7,
            1 => 'personal-data',
            2 => 'citizen',
            3 => ['register', 'register-alien'],
            4 => 'verify',
            5 => 'offer',
            6 => 'rules',
            7 => 'link'
        ],
        'd' => [
            'personal-data'         => 1,
            'citizen'               => 2,
            'register'              => 3,
            'register-alien'        => 3,
            'verify'                => 4,
            'offer'                 => 5,
            'rules'                 => 6,
            1 => 'personal-data',
            2 => 'citizen',
            3 => ['register', 'register-alien'],
            4 => 'verify',
            5 => 'offer',
            6 => 'rules',
        ]
    );

    private $steps = array (
        'w' => [
            'personal-data'     => 1,
            'citizen'           => 1,
            'register'          => 1,
            'register-alien'    => 1,
            'verify'            => 2,
            'offer'             => 3,
            'rules'             => 3,
            'link'              => 3
        ],
        'd' => [
            'personal-data'     => 1,
            'citizen'           => 1,
            'register'          => 1,
            'register-alien'    => 1,
            'verify'            => 2,
            'offer'             => 3,
            'rules'             => 3,
        ]
    );

    private $currentPage, $progress, $sessionKey, $userType;

    public function set($page, $userType) {
        if ($userType == 'w') {
            $this->sessionKey = [
                'progress' => 'registrationProgressW',
                'step' => 'registrationClosedStepW'
            ];
            $this->userType = 'w';
        }
        if ($userType == 'd') {
            $this->sessionKey = [
                'progress' => 'registrationProgressD',
                'step' => 'registrationClosedStepD'
            ];
            $this->userType = 'd';
        }

        if (! Session::exists($this->sessionKey['progress'])) {
            Session::put($this->sessionKey['progress'], array());
            Session::save();
        }
        $this->currentPage = $page;
        $this->progress = Session::get($this->sessionKey['progress']);
    }

    public function currentPageIsFirstPage() {
        return ($this->currentPage == $this->pages[$this->userType][1]);
    }

    public function prevPageFinished()
    {
        if ($this->currentPageIsFirstPage()) {
            return false;
        }

        $prevPageNum = $this->pages[$this->userType][$this->currentPage] - 1;
        return in_array($prevPageNum, $this->progress);
    }

    public function requestStepIsClosed() {
        if (!Session::exists($this->sessionKey['step'])) {
            return false;
        }
        $cs = Session::get($this->sessionKey['step']);
        return ($this->steps[$this->userType][$this->currentPage] <= $cs);
    }

    public function getResumePageNameWithParams() {
        // если progress пустой, то вернется первая страница
        $page = $this->pages[$this->userType][end($this->progress) + 1];
        $params = array('user_type' => $this->userType);

        if ($page == 'verify' && Session::exists('phone_call')) {
            $params['phone_call'] = Session::get('phone_call');
        }
        return array(
            'name' => $page,
            'params' => $params
        );
    }

    // Отрабатывает после того как страница шага регистрации была загружена
    // а значит сработал middleware, проверяющий очередность.
    // И после того, как пользователь сделал все необходимые действия на странице
    // и нажал "Продолжить".
    public function save(Request $request)
    {
        if (! $request->exists('page')) {
            return response([
                'error' => true,
                'errors' => array(
                    'msg' => 'Системная ошибка: передайте параметр page'
                )
            ], 400);
        }
        if (! $request->exists('user_type')) {
            return response([
                'error' => true,
                'errors' => array(
                    'msg' => 'Системная ошибка: передайте параметр user_type'
                )
            ], 400);
        }

        $this->set($request->page, $request->user_type);

        $progressNum = $this->pages[$this->userType][$request->page];
        $progress = Session::exists($this->sessionKey['progress'])?
            Session::get($this->sessionKey['progress']) : array();

        if (! in_array($progressNum, $progress)) {
            array_push($progress, $progressNum);
            Session::put($this->sessionKey['progress'], $progress);
            if ($request->page == 'register')
            {
                Session::put($this->sessionKey['step'],
                    $this->steps[$this->userType][$request->page]
                );
                Session::put('phone_call', $request->phone_call);
            }
            else if (   $request->page == 'verify' ||
                        $request->page == 'link' )
            {
                Session::put($this->sessionKey['step'],
                    $this->steps[$this->userType][$request->page]
                );
            }
            Session::save();
        }
//dd(Session::get('registrationProgress'));
        return response([], 200);
    }

    public function check(Request $request) {
        $this->set($request->page, $request->user_type);

        if (  $this->currentPageIsFirstPage() &&
            ! $this->requestStepIsClosed() )
        {
            return response()->json([
                'redirect' => 0
            ]);
        }

        if ($this->prevPageFinished()) {
            if ($this->requestStepIsClosed()) {

                $page = $this->getResumePageNameWithParams();
                return response([
                    'redirect'  => 1,
                    'page_name' => $page['name'],
                    'params'    => $page['params']
                ], 200);
            }
            else {
                return response()->json([
                    'redirect' => 0
                ]);
            }
        }
        else {
            $page = $this->getResumePageNameWithParams();

            return response([
                'redirect'  => 1,
                'page_name' => $page['name'],
                'params'    => $page['params']
            ], 200);
        }
    }

}