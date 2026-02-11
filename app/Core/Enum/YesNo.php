<?php


namespace App\Core\Enum;


class YesNo extends BaseEnum
{

    protected static $enumList = array(
        'YES' => array('value' => 'yes', 'description' => 'Yes'),
        'NO' => array('value' => 'no', 'description' => 'No'),
    );
}
