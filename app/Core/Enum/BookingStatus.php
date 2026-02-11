<?php

namespace App\Core\Enum;

use App\Core\Enum\BaseEnum;

class BookingStatus extends BaseEnum
{

    protected static $enumList = array(
        'PENDING'=>array('value'=>'1','description'=>'Pending', 'colorCode'=>'#FFC107'),
        'CONFIRMED'=>array('value'=>'2','description'=>'Confirmed', 'colorCode'=>'#4CAF50'),
        'CANCELLED'=>array('value'=>'3','description'=>'Cancelled', 'colorcode'=>'#F44336'),
        'EXPIRED'=>array('value'=>'4','description'=>'Expired', 'colorcode'=>'#F44336'),
    );

}
