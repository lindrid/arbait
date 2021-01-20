<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerOccupationsExperienceController extends Controller
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find($request->user_id);

        foreach ($request->radiobtn_values as $occupationId => $xpTime) {
            //$user->occupations()->sync([$occupationId => ['xp_time' => $xpTime]]);
            DB::table('occupation_user')
                ->where('user_id', $user->id)
                ->where('occupation_id', $occupationId)
                ->update(array('xp_time' => $xpTime));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $occupations = $user->occupations->all();
        $radiButtonsInVue = array(
            'class_names' => array(),
            'values' => array()
        );

        foreach ($occupations as $occ) {
            $radiButtonsInVue['class_names'][$occ->id] = "xp_time_$occ->id";
            $radiButtonsInVue['values'][$occ->id] = 0;
        }

        return response()->json([
            'occupations' => $occupations,
            'radiobtn_class_names' => $radiButtonsInVue['class_names'],
            'radiobtn_values' => $radiButtonsInVue['values']
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
