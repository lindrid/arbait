<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Occupation;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('skills')->insert( array(
            array(
                'name'      => 'Прием товара',
                'occupation_id' => Occupation::where('name','Кладовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                'name'      => 'Распределение товара на складе',
                'occupation_id' => Occupation::where('name','Кладовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                'name'      => 'Выдача товара в торговый зал',
                'occupation_id' => Occupation::where('name','Кладовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 4,
                'name'      => 'Учет товара, инвентаризация',
                'occupation_id' => Occupation::where('name','Кладовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 5,
                'name'      => 'Поддержание чистоты и порядка на складе',
                'occupation_id' => Occupation::where('name','Кладовщик')->first()->id,
                'parent_id' => 0
            ),

            /****************************************************/
            array(
                //'id'        => 6,
                'name'      => 'Участие в погрузочно-разгрузочных работах',
                'occupation_id' => Occupation::where('name','Комплектовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 7,
                'name'      => 'Размещение принятого товара на складе',
                'occupation_id' => Occupation::where('name','Комплектовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 8,
                'name'      => 'Сборка и подготовка товара к отправке согласно сопроводительным документам',
                'occupation_id' => Occupation::where('name','Комплектовщик')->first()->id,
                'parent_id' => 0
            ),

            /****************************************************/
            array(
                //'id'        => 9,
                'name'      => 'Приемка товара',
                'occupation_id' => Occupation::where('name','Сортировщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 10,
                'name'      => 'Погрузо-разгрузочные работы',
                'occupation_id' => Occupation::where('name','Сортировщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 11,
                'name'      => 'Сборка товара по накладным',
                'occupation_id' => Occupation::where('name','Сортировщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 12,
                'name'      => 'Подготовка товара к доставке (упаковка)',
                'occupation_id' => Occupation::where('name','Сортировщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 13,
                'name'      => 'Участие в проведении инвентаризации',
                'occupation_id' => Occupation::where('name','Сортировщик')->first()->id,
                'parent_id' => 0
            ),

            /****************************************************/
            array(
                //'id'        => 14,
                'name'      => 'Работы на морском транспорте',
                'occupation_id' => Occupation::where('name','Такелажник')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 15,
                'name'      => 'Трапы',
                'occupation_id' => Occupation::where('name','Такелажник')->first()->id,
                'parent_id' => 14
            ),
            array(
                //'id'        => 16,
                'name'      => 'Канаты',
                'occupation_id' => Occupation::where('name','Такелажник')->first()->id,
                'parent_id' => 14
            ),
            array(
                //'id'        => 17,
                'name'      => 'Пластыри',
                'occupation_id' => Occupation::where('name','Такелажник')->first()->id,
                'parent_id' => 14
            ),

            array(
                //'id'        => 18,
                'name'      => 'Навыками работы с ручным инструментом и оборудованием',
                'occupation_id' => Occupation::where('name','Такелажник')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 19,
                'name'      => 'Сборка товара по накладным',
                'occupation_id' => Occupation::where('name','Такелажник')->first()->id,
                'parent_id' => 0
            ),

            /******************** Тальман ********************************/
            array(
                //'id'        => 20,
                'name'      => 'прием/выдача грузов на склад и со склада',
                'occupation_id' => Occupation::where('name','Тальман')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 21,
                'name'      => 'Оформление тальманских расписок',
                'occupation_id' => Occupation::where('name','Тальман')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 22,
                'name'      => 'актирование брака и дефектов в процессе погрузки/выгрузки',
                'occupation_id' => Occupation::where('name','Тальман')->first()->id,
                'parent_id' => 0
            ),

            /******************** Упаковщик ********************************/
            array(
                //'id'        => 23,
                'name'      => 'Упаковка товара',
                'occupation_id' => Occupation::where('name','Упаковщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 24,
                'name'      => 'Маркировка готовой продукции',
                'occupation_id' => Occupation::where('name','Упаковщик')->first()->id,
                'parent_id' => 0
            ),


            /******************** Фасовщик ********************************/
            array(
                //'id'        => 25,
                'name'      => 'Фасовка товара',
                'occupation_id' => Occupation::where('name','Фасовщик')->first()->id,
                'parent_id' => 0
            ),


            /*********************** Арматурщик - кузовщик  *****************************/
            array(
                //'id'        => 26,
                'name'      => 'Сварочные работы',
                'occupation_id' => Occupation::where('name','Арматурщик - кузовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 27,
                'name'      => 'Жестяные работы',
                'occupation_id' => Occupation::where('name','Арматурщик - кузовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 28,
                'name'      => 'Рихтовочные работы',
                'occupation_id' => Occupation::where('name','Арматурщик - кузовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 29,
                'name'      => 'Замена кузовных деталей',
                'occupation_id' => Occupation::where('name','Арматурщик - кузовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 30,
                'name'      => 'Маскировка/оклейка',
                'occupation_id' => Occupation::where('name','Арматурщик - кузовщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 31,
                'name'      => 'Стапельные работы',
                'occupation_id' => Occupation::where('name','Арматурщик - кузовщик')->first()->id,
                'parent_id' => 0
            ),


            /*********************** Бетонщик-Арматурщик  *****************************/
            array(
                //'id'        => 32,
                'name'      => 'Установка опалубки',
                'occupation_id' => Occupation::where('name','Бетонщик-Арматурщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 33
                'name'      => 'Вязка арматуры',
                'occupation_id' => Occupation::where('name','Бетонщик-Арматурщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 34,
                'name'      => 'Заливка бетона',
                'occupation_id' => Occupation::where('name','Бетонщик-Арматурщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 35,
                'name'      => 'Бетонные работы',
                'occupation_id' => Occupation::where('name','Бетонщик-Арматурщик')->first()->id,
                'parent_id' => 0
            ),

            /*********************** Бетонщик-Монтажник  *****************************/
            array(
                //'id'        => 36,
                'name'      => 'Заливка бетона',
                'occupation_id' => Occupation::where('name','Бетонщик-Монтажник')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 37
                'name'      => 'Сборка металлоконструкций',
                'occupation_id' => Occupation::where('name','Бетонщик-Монтажник')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 38,
                'name'      => 'Бетонные работы',
                'occupation_id' => Occupation::where('name','Бетонщик-Монтажник')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 39,
                'name'      => 'Очистка, окраска шпунта',
                'occupation_id' => Occupation::where('name','Бетонщик-Монтажник')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 40,
                'name'      => 'Приемка бетона',
                'occupation_id' => Occupation::where('name','Бетонщик-Монтажник')->first()->id,
                'parent_id' => 0
            ),

            /*********************** Газосварщик  *****************************/
            array(
                //'id'        => 41,
                'name'      => 'Ремонт, изготовление и замена металлических конструкций распределительных подстанций',
                'occupation_id' => Occupation::where('name','Газосварщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 42
                'name'      => 'Изготовление изделий для прокладки кабельных и проводных линий',
                'occupation_id' => Occupation::where('name','Газосварщик')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 43,
                'name'      => 'Резка металла',
                'occupation_id' => Occupation::where('name','Газосварщик')->first()->id,
                'parent_id' => 0
            ),

            /*********************** Дорожный рабочий  *****************************/
            array(
                //'id'        => 44,
                'name'      => 'Строительство дороги',
                'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 45
                'name'      => 'Погрузка, выгрузка и внутри-складская переработка грузов',
                'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                'parent_id' => 0
            ),
            array(
                //'id'        => 46,
                'name'      => 'Сортировка, укладка, переноска, перевеска, фасовка',
                'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                'parent_id' => 0
            ),

            array(
                //'id'        => 47,
                'name'      => 'Установка приспособлений для погрузки и выгрузки груза',
                'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                'parent_id' => 0
            ),
                array(
                    //'id'        => 48,
                    'name'      => 'Лебедок',
                    'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                    'parent_id' => 47
                ),
                array(
                     //'id'        => 49,
                    'name'      => 'Подъемных блоков',
                    'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                    'parent_id' => 47
                ),
                array(
                    //'id'        => 50,
                    'name'      => 'Устройств временных скатов',
                    'occupation_id' => Occupation::where('name','Дорожный рабочий')->first()->id,
                    'parent_id' => 47
                ),


        ));

        DB::table('skills')->insert( array(
            array(
                'name'      => 'Участие в инвентаризациях',
                'occupation_id' => Occupation::where('name','Комплектовщик')->first()->id,
                'parent_id' => 0,
                'same_skill_id' => 4
            ),
            array(
                'name'      => 'Поддержание чистоты и порядка на складе',
                'occupation_id' => Occupation::where('name','Комплектовщик')->first()->id,
                'parent_id' => 0,
                'same_skill_id' => 5
            )
        ));
    }
}
