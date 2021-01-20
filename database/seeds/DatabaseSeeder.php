<?php

use Illuminate\Database\Seeder;
use App\Payment;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(OccupationsTableSeeder::class);
        //$this->call(SkillsTableSeeder::class);
        $this->seedRolesTable();
        $this->seedPrivilegesTable();
        $this->seedPrivilegeRoleTable();
    }

    private function seedRolesTable()
    {
        DB::table('roles')->insert( array(
            [
                'id'    => 1,
                'name' => 'Супер пользователь',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 2,
                'name' => 'Диспетчер',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 3,
                'name' => 'Рекламщик',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 4,
                'name' => 'Гость',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ));
    }

    private function seedPrivilegesTable()
    {
        DB::table('privileges')->insert( array(
            [
                'id'    => 1,
                'name' => 'Создавать заявки',
                'term'  => 'CreateApplications',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 2,
                'name' => 'Удалять заявки',
                'term'  => 'DeleteApplications',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 3,
                'name' => 'Изменять заявки',
                'term'  => 'EditApplications',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 4,
                'name' => 'Добавлять,редактировать,удалять исполнителей в заявке',
                'term'  => 'ChangeWorkersInApplications',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 5,
                'name' => 'Видеть заявки',
                'term'  => 'WatchApplications',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 6,
                'name' => 'Видеть личные данные исполнителей',
                'term'  => 'WatchWorkersPersonalData',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 7,
                'name' => 'Видеть личные данные заказчиков',
                'term'  => 'WatchClientsPersonalData',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 8,
                'name' => 'Смотреть отчет и детализацию по грузчикам',
                'term'  => 'WatchMoversReport',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 9,
                'name' => 'Вносить изменения в отчет по грузчикам',
                'term'  => 'EditMoversReport',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 10,
                'name' => 'Изменять детализацию по грузчикам',
                'term'  => 'EditMoversDetailing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 11,
                'name' => 'Смотреть отчет и детализацию по инстраграмму',
                'term'  => 'WatchInstaReport',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 12,
                'name' => 'Вносить изменения в отчет по инстраграмму',
                'term'  => 'EditInstaReport',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'    => 13,
                'name' => 'Изменять детализацию по инстаграмму',
                'term'  => 'EditInstaDetailing',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ));
    }


    private function seedPrivilegeRoleTable()
    {
        DB::table('privilege_role')->insert( array(
        /*********   Супер пользователь  ***********/
            [
                'privilege_id'  => 1,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 2,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 3,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 4,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 5,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 6,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 7,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 8,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 9,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 10,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 11,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 12,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 13,
                'role_id'       => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        /*********   Диспетчер  ***********/
            [
                'privilege_id'  => 1,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 3,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 4,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 5,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 6,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 7,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 8,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 9,
                'role_id'       => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        /*********   Рекламщик  ***********/
            [
                'privilege_id'  => 11,
                'role_id'       => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 12,
                'role_id'       => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'privilege_id'  => 13,
                'role_id'       => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],

        /*********   Гость   ***********/
            [
                'privilege_id'  => 5,
                'role_id'       => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ));
    }

    private function seedDistrictsTable()
    {
        DB::table('districts')->insert( array(
            array(  'name'      => 'Заря, вторяк'),
            array(  'name'      => 'Столетие, фирсова, бам, молодежная'),
            array(  'name'      => 'Снеговая падь'),
            array(  'name'      => 'Первая речка'),
            array(  'name'      => 'Некрасовская, гоголя, центр'),
            array(  'name'      => 'Третья рабочая, толстого, шилкинская'),
            array(  'name'      => 'Чуркин'),
            array(  'name'      => 'Баляева'),
            array(  'name'      => 'Луговая'),
            array(  'name'      => 'Нейбута'),
            array(  'name'      => 'Спортивная/клубная'),
            array(  'name'      => 'Тихая'),
            array(  'name'      => 'Эгершельд'),
            array(  'name'      => 'Остров русский'),
            array(  'name'      => 'Пригород'),
        ));
    }

    private function seedDebetCardsTable()
    {
        $p = new Payment();
        DB::table('debet_cards')->insert( array(
            array
            (
                'id'        => $p->dimasSberCardId(),
                'number'    => '4276 5000 2844 9583',
                'bank'      => 'sb',
                'sent'      => 0
            ),
            array
            (
                'id'        => $p->mySberCardId(),
                'number'    => '4276 5000 4079 5203',
                'bank'      => 'sb',
                'sent'      => 0
            ),
            array
            (
                'id'        => $p->mtsCardId(),
                'number'    => '5246 0291 0194 1146',
                'bank'      => 'mt',
                'sent'      => 0
            ),
            array
            (
                'id'        => $p->tinkoffCardId(),
                'number'    => '5536 9138 2386 8225',
                'bank'      => 'ti',
                'sent'      => 0
            ),
        ));
    }
}