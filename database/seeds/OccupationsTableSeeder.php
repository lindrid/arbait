<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Occupation;

class OccupationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('occupations')->insert( array(
            array(  'name'      => Occupation::mainCategory(),   // id = 1
                'parent_id' => 1),
            array(  'name'      => 'Склад',             // id = 2
                'parent_id' => 2),
            array(  'name'      => 'Ремонты/стройка',   // id = 3
                'parent_id' => 3),

            array(  'name'      => 'Грузчик',
                'parent_id' => 1),

            array(  'name'      => 'Разнорабочий',
                'parent_id' => 1),

        ));


        DB::table('occupations')->insert( array(
            array(  'name'      => 'Кладовщик',
                'parent_id' => Occupation::where('name','Склад')->first()->id),

            array(  'name'      => 'Комплектовщик',
                'parent_id' => Occupation::where('name','Склад')->first()->id),

            array(  'name'      => 'Сортировщик',
                'parent_id' => Occupation::where('name','Склад')->first()->id),

            array(  'name'      => 'Такелажник',
                'parent_id' => Occupation::where('name','Склад')->first()->id),

            array(  'name'      => 'Тальман',
                'parent_id' => Occupation::where('name','Склад')->first()->id),

            array(  'name'      => 'Упаковщик',
                'parent_id' => Occupation::where('name','Склад')->first()->id),

            array(  'name'      => 'Фасовщик',
                'parent_id' => Occupation::where('name','Склад')->first()->id),
        ));


        DB::table('occupations')->insert( array(
            array(  'name'      => 'Арматурщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Бетонщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Кровельщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Маляр',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Монтажник',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Отделочник',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Плотник',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),
        ));


        DB::table('occupations')->insert( array(

            array(  'name'      => 'Газосварщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Дорожный рабочий',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Жестянщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Изолировщик (гидроизоляция)',   // id = 21
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Каменщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),


            array(  'name'      => 'Арматурщик - кузовщик',
                'parent_id' => Occupation::where('name','Арматурщик')->first()->id),

            array(  'name'      => 'Бетонщик-Арматурщик',
                'parent_id' => Occupation::where('name','Арматурщик')->first()->id),



            array(  'name'      => 'Бетонщик-Арматурщик',
                'parent_id' => Occupation::where('name','Бетонщик')->first()->id),

            array(  'name'      => 'Бетонщик-монтажник',
                'parent_id' => Occupation::where('name','Бетонщик')->first()->id),


            /******* Кровельщик *******************/
            array(  'name'      => 'Кровельщик',
                'parent_id' => Occupation::where('name','Кровельщик')->first()->id),

            array(  'name'      => 'Плотник-кровельщик',
                'parent_id' => Occupation::where('name','Кровельщик')->first()->id),


            /******* Маляр *******************/
            array(  'name'      => 'Маляр',
                'parent_id' => Occupation::where('name','Маляр')->first()->id),

            array(  'name'      => 'Маляр-плотник',
                'parent_id' => Occupation::where('name','Маляр')->first()->id),

            array(  'name'      => 'Маляр-штукатур',
                'parent_id' => Occupation::where('name','Маляр')->first()->id),



            array(  'name'      => 'Мастер общестроительных работ',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Мастер отделочных работ',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),



            /******* Монтажник *******************/
            array(  'name'      => 'Бетонщик-монтажник',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник вентиляции',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник металлоконструкций',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник натяжных потолков',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник окон ПВХ',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник радиоэлектронной аппаратуры',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник связи',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Монтажник слаботочных систем',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Сборщик-монтажник',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Сварщик-монтажник',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),

            array(  'name'      => 'Слесарь-монтажник',
                'parent_id' => Occupation::where('name','Монтажник')->first()->id),


            /******* Отделочник *******************/
            array(  'name'      => 'Отделочник-универсал',
                'parent_id' => Occupation::where('name','Отделочник')->first()->id),

            array(  'name'      => 'Отделочник-фасадчик',
                'parent_id' => Occupation::where('name','Отделочник')->first()->id),



            array(  'name'      => 'Плиточник',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),



            /******* Плотник *******************/
            array(  'name'      => 'Плотник',
                'parent_id' => Occupation::where('name','Плотник')->first()->id),

            array(  'name'      => 'Плотник-столяр',
                'parent_id' => Occupation::where('name','Плотник')->first()->id),

            array(  'name'      => 'Плотник-кровельщик',
                'parent_id' => Occupation::where('name','Плотник')->first()->id),



            array(  'name'      => 'Сантехник',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Слесарь',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Установщик дверей, окон',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Штукатур',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Электрик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Электрогазосварщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Электромеханик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Электромонтажник',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Электромонтер',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id),

            array(  'name'      => 'Электросварщик',
                'parent_id' => Occupation::where('name','Ремонты/стройка')->first()->id)
        ));

        /** Повторяющиеся специальности *****/
        DB::table('occupations')
            ->where('name', 'Бетонщик-Арматурщик')
            ->where('id', Occupation::where('name', 'Бетонщик-Арматурщик')->first()->id)
            ->update(
                array(
                    'same_occ_id' => Occupation::where('name', 'Бетонщик-Арматурщик')
                        ->orderBy('id', 'desc')->first()->id,
                )
            );
        DB::table('occupations')
            ->where('name','Бетонщик-монтажник')
            ->where('id', Occupation::where('name', 'Бетонщик-монтажник')->first()->id)
            ->update(
                array(
                    'same_occ_id' => Occupation::where('name', 'Бетонщик-монтажник')
                        ->orderBy('id', 'desc')->first()->id,
                )
            );
    }
}
