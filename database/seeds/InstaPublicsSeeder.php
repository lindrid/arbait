<?php

use Illuminate\Database\Seeder;
use App\InstaPublic;
use Carbon\Carbon;

class InstaPublicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('insta_publics')->insert([
            [   'id' => InstaPublic::ID_RABOTA_VDK,
                'name' => InstaPublic::NAME_RABOTA_VDK,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [   'id' => InstaPublic::ID_MODELS_VDK,
                'name' => InstaPublic::NAME_MODELS_VDK,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ]);
    }
}
