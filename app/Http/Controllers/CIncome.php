<?php

namespace App\Http\Controllers;

use App\Income;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CIncome extends Controller
{
    public function index()
    {
        return Income::all();
    }

   /* public function indexUser()
    {
        $user = JWTAuth::parseToken()->authenticate()->name;
        $posts = Company::where("user_name", "=", $user)->get();

        return response()->json($posts);
    }*/

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

    public function store(Request $request)
    {
        $income = Income::updateOrCreate(
            ['text' => trim($request->get('text'))],
            [
                'text' => trim($request->get('text')),
                'money_amount' => $request->get('money_amount')
            ]
        );

        $allRowsToday = Income::whereDate('updated_at', Carbon::today())->get();
        $todayIncome = 0;

        foreach($allRowsToday as $incomeRow){
            $todayIncome += $incomeRow->money_amount;
        }

        $allRowsMonth = Income::whereDate(
            'updated_at', '>=', Carbon::now()->firstOfMonth()->toDateTimeString()
        )->get();
        $thisMonthIncome = 0;

        foreach($allRowsMonth as $incomeRow2){
            $thisMonthIncome += $incomeRow2->money_amount;
        }

        return response([
            'day_income' => $todayIncome,
            'month_income' => $thisMonthIncome,
        ], 200);
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