<?php

namespace App\Http\Controllers\Accountancy;

use App\Application;
use App\Payment;
use App\Company;
use App\Http\Controllers\Controller;

use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Carbon\Carbon;

class CPayment extends Controller
{
    // данные для страницы
    // платежей
    public function index($date = 'today')
    {
        if ($date == 'today') {
            $date = Carbon::today()->toDateString();
        }

        $apps = Application::whereDate('date', $date)
            ->orderBy('created_at', 'desc')
            ->get();
        $dayApplicationCount = 0;
        $payments = [];
        foreach ($apps as $app) {
            $payments[$app->id] = $app->payments;
            $appAccIsOpen[$app->id] = true;
            $dayApplicationCount++;
        }

        return [
            'apps'                  =>      $apps,
            'payments'              =>      $payments,
            'date'                  =>      $date,
            'app_count'             =>      $dayApplicationCount,
            'app_accordion_open'    =>      $appAccIsOpen
        ];
    }

    public function show($id)
    {
        return Company::findOrFail($id);

    }

    /*public function show(Request $request, Company $post)
    {
        $user = JWTAuth::parseToken()->authenticate()->name;
        $posts = Company::where("user_names", "=", $user->name)->get();

        return response()->json($posts);
    }*/

    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->update($request->all());

        return $company;
    }

    public function add (Request $request)
    {
        $waName = "Диспетчер Глеб: ";
        $strings = explode("\n", $request->get('text'));
        $apps = $payments = array();
        $appIndex = 0;
        $paymentIndex = 1;
        $itWas = 'nothing';
        $errors = $appsErrors = $cIndexes = $continueIndexes = [];
        $wrongPlace = ' не на том месте';

        $debug_lengths = [];
        $debug_continueIndexes = [];
        for($i = 0; $i < count($strings); $i++)
        {
            if (in_array($i, $continueIndexes)) {
                array_push($debug_continueIndexes, $i);
                continue;
            }

            if ((strpos($strings[$i], "Деньги") !== false))
            {
                $appIndex++;
                if (!($itWas == 'nothing' || $itWas == 'money_amount')) {
                    $errors[$i]['message'] = 'Строка Деньги' . $wrongPlace;
                }
                $paymentIndex = 1;

                $addr = Str::after($strings[$i+1], $waName);
                array_push($continueIndexes, $i+1);
                $incAddition = 2;

                if (preg_match("/^ЕДГ[ ]*❗*$/iu", $addr)) {
                    $apps[$appIndex]['EDG'] = true;
                    array_push($continueIndexes, $i+2);
                    $addr = Str::after($strings[$i+2], $waName);
                    $incAddition = 3;
                }

                $err = false;
                if (strpos($strings[$i + $incAddition], $waName) === false) {
                    $s = Str::after($strings[$i + $incAddition], $waName);
                    if (strlen($s) <= 5) {
                        if (!is_numeric($s)) {
                            for ($j = 0; $j < strlen($s); $j++) {
                                if (!is_numeric($s[$j])) {
                                    if ($s[$j] != 'р') {
                                        $err = true;
                                        $errors[$i]['message'] = 'Недопустимый символ в доходе от заявки:' .
                                            $s[$j];
                                        $errors[$i]['address'] = $apps[$appIndex]['address'];
                                        break;
                                    }
                                }
                            }
                        }

                        if (!$err) {
                            $apps[$appIndex]['income'] = str_replace('р', '', $s);
                        }
                        array_push($continueIndexes, $i + $incAddition);
                    }
                }
                else {
                    $apps[$appIndex]['income'] = 0;
                }

                $apps[$appIndex]['address'] = $addr;
                $itWas = 'application_address';
            }
            else  {
                $s = str_replace(' ','', Str::after($strings[$i], $waName));
                $sl = strlen($s);
                array_push($debug_lengths, $sl);

                if (preg_match("/^ЕДГ[ ]*❗*$/iu", $s)) {
                    $apps[$appIndex]['EDG'] = true;
                    continue;
                }

                if ($sl <= 6) {
                    if (!($itWas == 'card_number' || $itWas == 'tel_number')) {
                        $errors[$i]['message'] = 'Сумма денег' . $wrongPlace;
                        $errors[$i]['address'] = $apps[$appIndex]['address'];
                    }
                    $firstNotNum = null;
                    for ($j = 0; $j < $sl; $j++) {
                        if (!is_numeric($s[$j])) {
                            $firstNotNum = $j;
                            break;
                        }
                    }
                    if (is_null($firstNotNum)) {
                        $payments[$appIndex][$paymentIndex]['money_amount'] =
                            $s;
                    }
                    else {
                        $payments[$appIndex][$paymentIndex]['money_amount'] =
                            substr($s, 0, $firstNotNum);
                    }
                    $paymentIndex++;
                    $itWas = 'money_amount';
                }
                else if ($sl >= 16) {
                    if (!($itWas == 'application_address' || $itWas == 'money_amount')) {
                        $errors[$i]['message'] = 'Номер карты' . $wrongPlace;
                        $errors[$i]['address'] = $apps[$appIndex]['address'];
                    }

                    $cardNumber = $s;
                    $firstNum = null;
                    for ($j = 0; $j < $sl; $j++) {
                        if (is_numeric($cardNumber[$j])) {
                            $firstNum = $j;
                            break;
                        }
                    }

                    $firstNotNum = null;
                    for ($j = $firstNum+1; $j < $sl; $j++) {
                        if (!is_numeric($cardNumber[$j])) {
                            $firstNotNum = $j;
                            break;
                        }
                    }

                    if (is_null($firstNum)) {
                        $errors[$i]['message'] = 'Это не номер карты!';
                        $errors[$i]['address'] = $apps[$appIndex]['address'];
                        continue;
                    }
                    else if ($firstNum > 0) {
                        $cardNumber = substr($cardNumber, $firstNum);
                    }

                    if (!is_null($firstNotNum)) {
                        $cardNumber = substr($cardNumber, 0, $firstNotNum);
                    }

                    $payments[$appIndex][$paymentIndex]['card_number'] = $cardNumber;
                    $itWas = 'card_number';
                }
                else {
                    if (!($itWas == 'application_address' || $itWas == 'money_amount')) {
                        $errors[$i]['message'] = 'Телефон карты' . $wrongPlace;
                        $errors[$i]['address'] = $apps[$appIndex]['address'];
                    }
                    $s = str_replace('-', '', $s);
                    if ($s[0] == '+') {
                        if ($s[1] != '7') {
                            $errors[$i]['message'] = 'Неверный формат номера телефона: '.$s;
                            $errors[$i]['address'] = $apps[$appIndex]['address'];

                        }
                        $s = Str::after($s, '7');
                    }
                    else {
                        if ($s[0] != '8') {
                            $errors[$i]['message'] = 'Неверный формат номера телефона: '.$s;
                            $errors[$i]['address'] = $apps[$appIndex]['address'];
                        }
                        $s = Str::after($s,'8');
                    }
                    $payments[$appIndex][$paymentIndex]['tel_number'] = $s;
                    $itWas = 'tel_number';
                }
            }
        }

        $errorCount = count($errors);
        if ($errorCount == 0) {
            DB::beginTransaction();
            foreach ($apps as $appIndex => $app) {
                $howMuchAlreadyExist = Application::where('address', $app['address'])->
                where('date', Carbon::today()->toDateString())->count();
                if ($howMuchAlreadyExist > 0) {
                    $appsErrors[$appIndex] = [
                        'message' => 'Запись по данной заявке уже существует в базе данных',
                        'address' => $app['address']
                    ];
                    continue;
                }
                $a = new Application();
                $a->text = '';
                $a->address = $app['address'];
                $a->date = Carbon::today()->toDateString();
                $a->edg = 1 ? in_array('EDG', $app) : 0;
                $a->state = 2; //закрыта. завершена будет после всех переводов
                $a->worker_count = 0;
                $a->income = $app['income'];
                $a->outcome = 0;
                $a->profit = 0;
                $a->save();

                foreach ($payments[$appIndex] as $i => $payment) {
                    try {
                        $a->outcome += $payment['money_amount'];
                        $p = new Payment();
                        $p->date = $a->date;
                        $p->state = 0; //открыт
                        $p->money_amount = $payment['money_amount'];
                        $p->receiver_card_number = $payment['card_number'];
                        $p->card_id = $p->defaultCardId();
                        $p->application_id = $a->id;
                        $p->save();
                    }
                    catch (\Exception $e) {
                        //$this->traceError($payments, 'addPayment');
                        return ['payments' => $payments, 'i' => $i, "exception" => $e,
                            'apps' => $apps, 'errors' => $errors, 'apps_errors' => $appsErrors,
                            'indexes' => $continueIndexes, 'strings' => $strings,'lengths' => $debug_lengths];
                    }
                }

                $a->profit = $a->income - $a->outcome;
                $a->save();
            }

            $appsErrorCount = count($appsErrors);
            if ($appsErrorCount == 0) {
                DB::commit();
            }
            else {
                DB::rollBack();
            }
        }

        if ($errorCount > 0 || $appsErrorCount > 0) {
            return response([
                'errors'        => $errors,
                'apps_errors'   => $appsErrors,
                'strings'       => $strings,
                'apps'          => $apps,
                'payments'      => $payments,
                'indexes'       => $cIndexes,
                'debug_lengths' => $debug_lengths,
                'continue_indexes' => $continueIndexes
            ], 400);
        }
        else {
            return [
                'strings'       => $strings,
                'apps'          => $apps,
                'payments'      => $payments,
                'indexes'       => $cIndexes
            ];
        }
    }

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        $company->delete();
        return '';
    }

    public function edit($id)
    {
        //
    }

    public function create()
    {
        //
    }
}