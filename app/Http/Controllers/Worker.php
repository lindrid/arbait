<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\WorkerPhone;
use App\DebitCard;
use Propaganistas\LaravelPhone\PhoneNumber;

class Worker extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function getDebitCard($phone)
    {
        $normPhone = PhoneNumber::make($phone)->ofCountry('RU');
        $workerPhones = WorkerPhone::where('number', $normPhone)->get();
            //->max('updated_at');
        if (count($workerPhones) >= 1) {
            $worker = $workerPhones[0]->worker;
            $workerDebitCard = $worker->debitCards()->latest('updated_at')->first();
            if ($workerDebitCard == null) {
                $dbCardNumber = '';
            }
            else {
                $dbCardNumber = $workerDebitCard->number;
            }
        }
        else {
            $dbCardNumber = '';
        }

        return [
            'debit_card' => $dbCardNumber
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
