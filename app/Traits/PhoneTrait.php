<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 08.07.2019
 * Time: 11:40
 */

namespace App\Traits;


trait PhoneTrait
{
    public function equalNumbers($phone1, $phone2)
    {
        return ($this->normilize($phone1) == $this->normilize($phone2));
    }

    private function normilize($phone) {
        $phone = str_replace('+7', '8', $phone);
        $phone = str_replace(' ', '', $phone);
        $phone = str_replace('-', '', $phone);
        return $phone;
    }

}