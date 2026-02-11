<?php

namespace App\Core\Enum;

use App\Core\Enum\BaseEnum;

class PaymentMode extends BaseEnum
{

    protected static $enumList = array(
        'CASH'=>array('value'=>'1','description'=>'Cash'),     
    );

}
