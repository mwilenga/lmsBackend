<?php

namespace App\Core\Enum;

class RegistrationType extends BaseEnum
{
    protected static $enumList = [
        'INDIVIDUAL' => ['value' => 'individual', 'description' => 'Individual'],
        'HUB' => ['value' => 'hub', 'description' => 'From Hub'],
    ];
}
