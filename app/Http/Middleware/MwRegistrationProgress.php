<?php

namespace App\Http\Middleware;

use App\Http\Controllers\CRegistrationProgress;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Closure;

class MwRegistrationProgress
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    /** если пользователь не прошел регистрацию и пытается
     * перепрыгнуть через одну или несколько страниц
     * вперед, система возвращает его на следующую страницу
     * за той, на которой он все подтвердил и нажал "Продолжить",
     * т.е. на еще непройденную страницу регистрации (resumePage).
     *
     *  Если, пользователь уже подтвердил свои паспортные данные
     *  и был сохранен в базе, то вернуться на предыдущие шаги
     *  регистрации, он не может и система будет его перебрасывать
     *  на текущий шаг и будет просить пройти регистрацию до конца.
     *
     *  Регистрация поделена на 3 шага и возвращаться назад
     *  можно только в пределах одного и того же шага.
     */
    public function handle($request, Closure $next)
    {
        //dd(['registrationProgress' => Session::get('registrationProgress'),
         //   'registrationClosedStep' => Session::get('registrationClosedStep')]);
        //dd([Session::get('registrationProgressD'),
          // Session::get('registrationProgressW')]);

        //Session::flush();
        //return;
        /*
        $rawPage = substr($request->getRequestUri(), 1);
        //dd ($page);
        //return;
        $secondSlash = strpos($rawPage, '/');
        if ($secondSlash !== false) {
            $page = substr($rawPage, 0, $secondSlash);
            $userType = substr($rawPage, $secondSlash+1, 1);
            if ($secondSlash+2 < strlen($rawPage) ) {
                $secondParam = substr($rawPage, $secondSlash+2,
                    strlen($rawPage)-($secondSlash+2)
                ); // always phone_call ??
            }
        }

        if ($secondSlash === false || (($userType != 'w') && ($userType != 'd'))) {
            return redirect(env('APP_URL'));
        }

        $regProgress = new CRegistrationProgress();
        $regProgress->set($page, $userType);
//dd([$page, $userType]);
//return;
        if (  $regProgress->currentPageIsFirstPage() &&
             ! $regProgress->requestStepIsClosed() )
        {
            return $next($request);
        }

        if ($regProgress->prevPageFinished()) {
            if ($regProgress->requestStepIsClosed()) {
                $page = $regProgress->getResumePageNameWithParams();
                $params = $page['params'];
                if (array_key_exists ('phone_call' , $params)) {
                    return redirect($page['name'] . '/' . $userType .
                        '/' . $params['phone_call']
                    );
                }
                else {
                    return redirect($page['name'] . '/' . $userType);
                }
            }
            else {
                return $next($request);
            }
        }
        else {
            $page = $regProgress->getResumePageNameWithParams();
            $params = $page['params'];
            if (array_key_exists ('phone_call' , $params)) {
                return redirect($page['name'] . '/' . $userType .
                    '/' . $params['phone_call']
                );
            }
            else {
                return redirect($page['name'] . '/' . $userType);
            }
           // return redirect($page['name'] . '/' . $page['param']);
        }*/
    }
}
