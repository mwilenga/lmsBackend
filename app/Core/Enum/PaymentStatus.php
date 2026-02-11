<?php

namespace App\Core\Enum;

use App\Core\Enum\BaseEnum;

class PaymentStatus extends BaseEnum
{

    protected static $enumList = array(
        'PENDING'=>array('value'=>'1','description'=>'Pending'),
        'PAID'=>array('value'=>'2','description'=>'Paid'),
        'NOT_PAID'=>array('value'=>'3','description'=>'Not Paid'),
        'CANCELLED'=>array('value'=>'4','description'=>'Cancelled'),
        'PARTIAL_PAYMENT'=>array('value'=>'5','description'=>'Partially Paid'),        
    );

}
