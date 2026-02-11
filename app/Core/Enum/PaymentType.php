<?php

namespace App\Core\Enum;

use App\Core\Enum\BaseEnum;

class PaymentType extends BaseEnum
{

    protected static $enumList = array(
        'CASH_PAYMENT'=>array('value'=>'1','description'=>'Cash Payment '),     
        'ONLINE_PAYMENT'=>array('value'=>'2','description'=>'Online Payment '),
        'BANK_TRANSFER'=>array('value'=>'3','description'=>'Bank Transfer  '),
         
    );

}
