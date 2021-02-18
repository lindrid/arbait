<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use App\Http\Requests\RegisterFormRequest;
use App\User;
use App\UserPhone;

use Propaganistas\LaravelPhone\PhoneNumber;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Session;

use App\Traits\PhoneTrait;

class AuthController extends Controller
{
    use PhoneTrait;

    private $user_inn;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['logout', 'login', 'registration']]);
    }

    public function index()
    {
        return User::all();
    }

    public function register(RegisterFormRequest $request)
    {
        try {
            
        }
        catch (\Exception $e) {
            return response([
                'error' => true,
                'errors' => array (
                    'main' => "Сбой соединения с базой данных, обратитесь к администратору"
                )
            ], 400);
        }

        $this->myValidation($request);

        $request->pass_series_number = str_replace(' ', '',
            $request->pass_series_number);
        $passport = array (
            'fullname'       => $request->fullname,
            'series_number'  => $request->pass_series_number,
            'code'           => $request->pass_code,
            'date'           => $request->pass_date,
            'birth_date'     => $request->birth_date,
        );

        $userWithSamePassport = User::where('pass_series_number',
            $passport['series_number'])->first();


        if ($userWithSamePassport &&
            (strpos($userWithSamePassport->type, $request->user_type) !== false))
        {
            $a = array(
                'w' => 'Рабочий',
                'd' => 'Диспетчер'
            );
            return response([
                'error' => true,
                'error_same_passport' => true,
                'errors' => array(
                    'pass_series_number' => $a[$request->user_type] .
                        " с таким паспортом уже зарегистрирован."
                )
            ], 400);
        }

        $verResult = $this->verifyPassport($passport);
        if ($verResult['error']) {
            return response()->json($verResult, 400);
        }
        //$userWithSamePassport = false;

        if (!$userWithSamePassport) {
            $user = new User;
            $user->fullname = trim($request->fullname);
            $user->phone_call = $request->phone_call;
            $user->phone_whatsapp = $request->phone_whatsapp;
            $user->pass_series_number = $request->pass_series_number;
            $user->birth_date = $request->birth_date;
            $user->pass_date = $request->pass_date;
            $user->pass_code = $request->pass_code;
            $user->inn = $this->user_inn;
            $user->address = trim($request->address);

            // $request->password приходит в виде хэша bscrypt'а с солью 12.
            // и снова вызываем bcrypt, а также вызываем bcrypt
            // ниже в функции login, иначе всегда получаем ошибку "Invalid Credentials"
            // а слать пароли в нешифрованном виде - негоже
            $user->password = bcrypt($request->password);

            $user->type = $request->user_type;
            $user->RF_citizen = 1;
            $user->approved = 0;
            $user->verified_phone = 0;
        }
        else {
            $user = $userWithSamePassport;
            $user->type .= $request->user_type;
        }

        DB::beginTransaction();
        try {
            $user->save();

            if ($this->equalNumbers($request->phone_call,
                                    $request->phone_whatsapp))
            {
                $userPhones = new UserPhone;

                $userPhones->user_id = $user->id;
                $userPhones->number = $request->phone_call;
                $userPhones->type = 'cw';
                $userPhones->save();
            }
            else {
                $userPhones = array (
                    [
                        'user_id' => $user->id,
                        'number' => $request->phone_call,
                        'type' => 'c'
                    ],
                    [
                        'user_id' => $user->id,
                        'number' => $request->phone_whatsapp,
                        'type' => 'w'
                    ]
                );

                DB::table('user_phones')->insert($userPhones);
            }
        }
        catch (QueryException $e) {
            report($e);
            DB::rollBack();
            return response([
                'error' => true,
                'errors' => array (
                    'main' => "Возникла ошибка в работе с базой данных, обратитесь к администратору"
                )
            ], 400);
        }
        DB::commit();

        (new CRegistrationProgress())->save($request);

        return response([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $phoneNum = $request->phone_call;
        $password = sha1($request->password);

        $user = User::where('phone_call', '=',
            PhoneNumber::make($phoneNum)->ofCountry('RU'))
            ->first();
        if ($user !== null && $user->password === $password)
        {
            session([
                'user' => $user,
                'user_is_logged_in' => true
            ]);

            return response([
                'status' => 'success',
            ]);
        }

        return response([
            'status' => 'неправильный телефон или пароль',
            //'password1' => $user->password,
            //'password2' => $password,
            //'password3' => $request->password,
           // 'hash_equals' => hash_equals($user->password, $password)
        ], 400);
    }

    public function logout()
    {
        if (Session::exists('user')) {
            Session::forget('user');
        }

        if (Session::exists('user_is_logged_in')) {
            Session::forget('user_is_logged_in');
        }

        return redirect('/apps');

        //JWTAuth::invalidate();
        /*return response([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);*/
    }

    public function user(Request $request)
    {
        //$user = User::find(Auth::user()->id);
        $user = session('user');
        return response([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function refresh()
    {
        return response([
            'status' => 'success'
        ]);
    }

    private function myValidation(Request $request)
    {
        // $this->traceError($request);
        $request->validate(
            [
                //'title' => 'bail|required|unique:posts|max:255',
                'name'          => 'min:3|max:25',
                'phone'         => 'min:11|max:17',
                'password'      => 'min:5|max:80',
            ],
            [
                'name.min'     => Lang::get('auth.min_name'),
                'name.max'     => Lang::get('auth.max_name'),
                'phone.max'    => Lang::get('auth.max_phone'),
                'password.max' => Lang::get('auth.max_password'),
            ]
        );
    }

    private function verifyPassport($passport) {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request(
                'GET',
                "https://www.passport-api.ru/api/1.0/result.php?token=25d9u1a3i8L6b3Q2IwH2B8W81y81LR2Kf" .
                "&inn=21" .
                "&fio="         . $passport['fullname'] .
                "&birthdate="   . $passport['birth_date'] .
                "&docnum="      . $passport['series_number'] .
                "&docdt="       . $passport['date'] .
                "&dcode="       . $passport['code'],
                ['exceptions' => false,
            'timeout' => 30]
            );
        }
        catch (RequestException $exception) {
            return array(
                'error' => true,
                'errors' => array (
                    'main' => 'Нет связи с сервисом проверки паспорта, обратитесь к администратору',
                )
            );
        }

        if ($response->getStatusCode() == 200) {
            $r = json_decode($response->getBody(), true);

            if ($r['error']['code'] == '200') {
                if ($r['result']['inn'] == "") {
                    return array(
                        'error' => true,
                        'errors' => array (
                            'fullname'              => 'Проверьте ФИО, серию и номер ' .
                                'паспорта и дату рождения, где-то ошибка',
                            'pass_series_number'    => 'Проверьте серию и номер паспорта, ФИО и ' .
                                'дату рождения, где-то ошибка',
                            'birth_date'            => 'Проверьте дату рождения, ФИО и серию ' .
                                'и номер паспорта, где-то ошибка'
                        )
                    );
                }
                else {
                    $this->user_inn = $r['result']['inn'];
                    return array(
                        'error' => false
                    );
                }
            }
            else if ($r['error']['code'] == '207') {
                return array(
                    'error' => true,
                    'errors' => array (
                        'fullname'              => 'Проверьте ФИО, серию и номер ' .
                            'паспорта и дату рождения, где-то ошибка',
                        'pass_series_number'    => 'Проверьте серию и номер паспорта, ФИО и ' .
                            'дату рождения, где-то ошибка',
                        'birth_date'            => 'Проверьте дату рождения, ФИО и серию ' .
                            'и номер паспорта, где-то ошибка'
                    )
                );
            }
        }
        else {
            return array(
                'error' => true,
                'errors' => array (
                    'passport' => 'Сбой(2) в системе проверки паспорта, обратитесь к администратору'
                )
            );
        }
    }

    private function traceError(Request $request)
    {
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "=======================");
        $dateTime = Carbon::now()->toDayDateTimeString();
        Storage::append('error.txt', "$dateTime, Error in AuthController");
        Storage::append('error.txt', "=======================");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', $request);
    }

    private function logInFile(array $var)
    {
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "=======================");
        $dateTime = Carbon::now()->toDayDateTimeString();
        Storage::append('error.txt', "$dateTime, Log data from AuthController");
        Storage::append('error.txt', "=======================");
        Storage::append('error.txt', "\n");
        foreach ($var as $key=>$val) {
            foreach ($val as $key2 => $val2) {
                Storage::append('error.txt', "key1: $key, " . "key2: $key2 => " . $val);
            }
        }
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /*
     * www.passport-api.ru
     *
     * REQUEST:
     * https://www.passport-api.ru/api/1.0/result.php?token=25d9u1a3i8L6b3Q2IwH2B8W81y81LR2Kf
     * &inn=21
     * &fio=Федоров Дмитрий Сергеевич
     * &birthdate=99.99.9999
     * &docnum=9999999999
     * &docdt=99.99.9999
     * &dcode=999-999
     *
     *
     * JSON response
     * {
     *      "token" :
     *      {
     *          "domain" : "",
     *          "status" : "true",
     *          "count" : 1
     *      },
     *      "balance" :
     *      {
     *          "price" : "0.07",
     *          "current" : "100.00",
     *          "residue" : "99.93"
     *      },
     *      "error" :
     *      {
     *          "code" : "200",
     *          "text" : "success"
     *      },
     *      "result" :
     *      {
     *          "request_id" : "4M221Xz6jL4h1wXHhx9a",
     *          "inn" : "253913255996",
     *          "info" : ""
     *      }
     * }
     *
     * Ошибка 1, при вводе неправильного docnum (ошибка в номере паспорта):
     *
     * {
     *      "token":
     *      {
     *          "domain":"",
     *          "status":"true",
     *          "count":0
     *      },
     *      "balance":
     *      {
     *          "price":"0.00",
     *          "current":"0.00",
     *          "residue":"0.00"
     *      },
     *      "error":
     *      {
     *          "code":"207",
     *          "text":"ИП Федоров Дмитрий Сергеевич, error in ser_num field"
     *      },
     *      "result":
     *      {
     *          "request_id":"97Blny4E5SNwCp3V173q",
     *          "inn":"",
     *          "info":""
     *      }
     * }
     *
     *
     * Ошибка 2 :
     *      - ошибка в серии паспорта
     *      - ошибка в ФИО
     *      - ошибка в дате рождения
     *  code 200 = success:
     * {
     *      "token":
     *      {
     *          "domain" : "",
     *          "status" : "true",
     *          "count" : 2
     *      },
     *      "balance" :
     *      {
     *          "price" : "0.07",
     *          "current" : "99.93",
     *          "residue" : "99.86"
     *      },
     *      "error" :
     *      {
     *          "code" : "200",
     *          "text" : "success"
     *      },
     *      "result" :
     *      {
     *          "request_id" : "KqVk61GuDL9aZ41XYyd4",
     *          "inn" : "",
     *          "info" : "Информация об ИНН не найдена. Рекомендуем проверить правильность введённых данных и повторить попытку поиска."
     *      }
     * }
     *
     *
     *
     *  Ошибка 3. Код подразделения и дата выдачи паспорта.
     *  При вводе отличного от того, что есть в паспорте кода подразделения или даты выдачи,
     *  на результат это не влияет. Т.е. проверки этих величин на соответствие
     *  величинам в реальном паспорте - нет.
     *
     * Ошибка 4. При отправке inn, отличного от 21:
     * {
     *      "token":
     *      {
     *          "domain":"",
     *          "status":"true",
     *          "count":0
     *      },
     *      "balance":
     *      {
     *          "price":"0.00",
     *          "current":"0.00",
     *          "residue":"0.00"
     *      },
     *      "error":
     *      {
     *          "code":"209",
     *          "text":"ИП Федоров Дмитрий Сергеевич, the parameter has an empty value (&ser_num= || &code= || &inn=)"
     *      }
     * }
     *
     * Ошибка 5.
     *      - Неверный формат даты birthdate=38.11.2012
     *      - Неверный формат даты docdt=07.17.2012
     * {
     *      "token":
     *      {
     *          "domain":"",
     *          "status":"true",
     *          "count":31
     *      },
     *      "balance":
     *      {
     *          "price":"0.07",
     *          "current":"97.90",
     *          "residue":"97.83"
     *      },
     *      "error":
     *      {
     *          "code":"200",
     *          "text":"success"
     *      },
     *      "result":
     *      {
     *          "request_id":"MUPH9RrJ5I6h5JDrEa1u",
     *          "inn":"",
     *          "info":""
     *      }
     * }
     *
     *
     */

}
