<?php

namespace App\Http\Controllers;

use App\Occupation;
use App\User;
use App\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class WorkerOccupationsAndSkillsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allOccupations =  Occupation::orderBy('name')->get();
        $skills = Skill::all();
        $skillsByOccId = array();
        $skillIsComplex = array();// a skill is complex, when it has nested skills
        $nestedSkills = array(); // array ( complexSkill => array(/*nested skills*/) )

        $firstLevelCategories = collect();
        $occupations = array();
        $occupationIsComplex = array();// an occupation is complex, when it has nested occupations
        $nestedOccupations = array();

        $occupationAccIsOpen = array();
        $occ_chkbox_is_checked = array();
        $skillAccIsOpen = array();
        $skillsInVue = array (
            'levels' => array (),
            'class_names' => array(),
            'colors' => array()
        );

        foreach ($allOccupations as $occ) {
            $occupationAccIsOpen[$occ->id] = false;
            $occ_chkbox_is_checked[$occ->id] = false;
            $occupationIsComplex[$occ->id] = false;

            if ($occ->id == $occ->parent_id) {
                $firstLevelCategories->push($occ);
                if ($occ->name == Occupation::mainCategory()) {
                    $occupationAccIsOpen[$occ->id] = true;
                }
            }
        }

        foreach ($allOccupations as $occ) {
            $pid = $occ->parent_id;

            if ($occ->id == $pid) {
                continue;
            }

            //специальность внутри категории
            if ($firstLevelCategories->contains('id', $pid)) {
                if (array_key_exists($pid, $occupations)) {
                    $occupations[$pid]->push($occ);
                }
                else {
                    $occupations[$pid] = collect();
                    $occupations[$pid]->push($occ);
                }
            }
            //специальность внутри специальности
            else {
                $occupationIsComplex[$pid] = true;
                if (array_key_exists($pid, $nestedOccupations)) {
                    $nestedOccupations[$pid]->push($occ);
                }
                else {
                    $nestedOccupations[$pid] = collect();
                    $nestedOccupations[$pid]->push($occ);
                }
            }
        }

        foreach ($skills as $skl) {
            $skillIsComplex[$skl->id] = false;
            $skillsInVue['levels'][$skl->id] = -1;
            $skillsInVue['colors'][$skl->id] = 'inherit';
            $skillsInVue['captures'][$skl->id] = "(Не указано)";
            $skillsInVue['class_names'][$skl->id] = "skill_$skl->id";
        }

        foreach ($skills as $skl) {
            //nested skill
            if ($skl->parent_id != 0) {
                $skillsInVue['class_names'][$skl->parent_id] = "complex_skill_$skl->parent_id";
                $skillsInVue['class_names'][$skl->id] = "nested_skill_$skl->id";
                $skillIsComplex[$skl->parent_id] = true;
                $skillAccIsOpen[$skl->parent_id] = false;
                if (array_key_exists($skl->parent_id, $nestedSkills)) {
                    $nestedSkills[$skl->parent_id]->push($skl);
                }
                else {
                    $nestedSkills[$skl->parent_id] = collect();
                    $nestedSkills[$skl->parent_id]->push($skl);
                }
            }
            //ordinary skill
            else {
                if (array_key_exists($skl->occupation_id, $skillsByOccId)) {
                    $skillsByOccId[$skl->occupation_id]->push($skl);
                }
                else {
                    $skillsByOccId[$skl->occupation_id] = collect();
                    $skillsByOccId[$skl->occupation_id]->push($skl);
                }
            }
        }

        return response()->json([
            'categories' => $firstLevelCategories,
            'occupations' => $occupations,
            'nested_occupations' => $nestedOccupations,
            'occupation_is_complex' => $occupationIsComplex,
            'occupation_acc_is_open' => $occupationAccIsOpen,
            'skill_acc_is_open' => $skillAccIsOpen,
            'skills'        => $skillsByOccId,
            'skill_is_complex' => $skillIsComplex,
            'nested_skills' => $nestedSkills,
            'skill_levels' => $skillsInVue['levels'],
            'skill_class_names' => $skillsInVue['class_names'],
            'skill_colors' => $skillsInVue['colors'],
            'skill_captures' => $skillsInVue['captures'],
            'chkbox_is_checked' => $occ_chkbox_is_checked
        ]);
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


    private function traceError(Request $request) {
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', "=======================");
        $dateTime = Carbon::now()->toDayDateTimeString();
        Storage::append('error.txt', "$dateTime, Error in WorkerOccupationsAndSkillsController.store");
        Storage::append('error.txt', "=======================");
        Storage::append('error.txt', "\n");
        Storage::append('error.txt', $request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * пытаемся создать для рабочего связи со специальностями
     * и скиллами, которые он натыкал в ConfirmWorker.vue
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = User::find(1);

        /* если в базе уже есть какие-то связи, а мы тут НЕ редактируем, а
         * создаем, значит что-то пошло не так (например, нажали кнопку сохрания
         * второй раз) и надо заканчивать выполнение
         */
        $foundSkillUserRow = DB::table('skill_user')
            ->where('user_id', $user->id);
        $foundOccUserRow = DB::table('occupation_user')
            ->where('user_id', $user->id);
        if (($foundSkillUserRow->count() > 0) ||
            ($foundOccUserRow->count() > 0))
        {
            $this->traceError($request);
            return;
        }

        $occs = array();
        foreach ($request->checked_occupations as $occId) {
            array_push($occs, [
                'user_id' => $user->id,
                'occupation_id' => $occId
            ]);
        }
        DB::table('occupation_user')->insert($occs);

        $userSkills = array();
        foreach ($request->skill_levels as $skillId => $skillLvl) {
            if ($skillLvl != -1) {
                array_push($userSkills, [
                    'user_id' => $user->id,
                    'skill_id' => $skillId,
                    'skill_level' => $skillLvl
                ]);
            }
        }
        DB::table('skill_user')->insert($userSkills);
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
