<?php

use Illuminate\Database\Seeder;
use App\Activity;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(with(new Activity)->getTable())->insert([
            [   'id'    => Activity::ID_MOVERS,
                'name'  => Activity::NAME_MOVERS,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [   'id'    => Activity::ID_INSTAGRAM,
                'name'  => Activity::NAME_INSTAGRAM,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
