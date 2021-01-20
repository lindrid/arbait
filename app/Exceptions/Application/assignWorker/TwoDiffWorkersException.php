<?php
/**
 * Created by PhpStorm.
 * User: Дмитрий
 * Date: 01.03.2020
 * Time: 2:15
 */

namespace App\Exceptions\Application\assignWorker;

use Exception;

class TwoDiffWorkersException  extends Exception
{
    private $phoneWhatsapp, $phoneWhatsappWorker;
    private $phoneCall, $phoneCallWorker;
    private $setWorkerBy;


    public function setWhatsappPhoneAndWorker($phone, $workerName)
    {
        $this->phoneWhatsapp = $phone;
        $this->phoneWhatsappWorker['name'] = $workerName;
        $this->setWorkerBy = 'name';
    }

    public function setCallPhoneAndWorker($phone, $workerName)
    {
        $this->phoneCall = $phone;
        $this->phoneCallWorker['name'] = $workerName;
        $this->setWorkerBy = 'name';
    }

    public function setWhatsappPhoneAndWorkerById($phone, $workerId)
    {
        $this->phoneWhatsapp = $phone;
        $this->phoneWhatsappWorker['id'] = $workerId;
        $this->setWorkerBy = 'id';
    }

    public function setCallPhoneAndWorkerById($phone, $workerId)
    {
        $this->phoneCall = $phone;
        $this->phoneCallWorker['id'] = $workerId;
        $this->setWorkerBy = 'id';
    }

    public function message()
    {
        $whatsVal = $this->phoneWhatsappWorker[$this->setWorkerBy];
        $callVal = $this->phoneCallWorker[$this->setWorkerBy];

        if ($this->setWorkerBy = 'id')
            $typeName = 'id номером';
        if ($this->setWorkerBy = 'name')
            $typeName = 'именем';

        return
            "В базе зарегистрированы два разных рабочих!<br />" .
            " На телефон в Whatsapp <b>{$this->phoneWhatsapp}</b> -".
                " рабочий с $typeName <b>$whatsVal</b>.<br>" .
            " На телефон для звонка <b>{$this->phoneCall}</b> -".
                " рабочий с $typeName <b>$callVal</b>.<br>" .
            " Выберите одно из трех: <br><ol>" .

            " &nbsp;&nbsp;&nbsp;" .
            " <b>1.</b> рабочий с $typeName <b>$callVal</b> едет".
                " вместе с рабочим с $typeName <b>$whatsVal</b>" .
                ", <b>кнопка ('+')</b><br>" .

            " &nbsp;&nbsp;&nbsp;" .
            " <b>2.</b> рабочий с $typeName <b>$callVal</b> едет".
                " от рабочего с $typeName <b>$whatsVal</b>" .
                ", <b>кнопка ('силуэт')</b><br>" .

            " &nbsp;&nbsp;&nbsp;" .
            " <b>3.</b> рабочий с $typeName <b>$whatsVal</b> едет".
                " с телефоном рабочего с $typeName <b>$callVal</b> " .
                ", <b>кнопка ('смартфон')</b><br />";
    }
}