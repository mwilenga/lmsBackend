<?php

namespace App\Core\Enum;

use App\Core\Enum\BaseEnum;

class FormMethod extends BaseEnum
{

    protected static $enumList = array(
        'CREATE' => array('value' => 'create', 'description' => 'Create'),
        'SUBMIT' => array('value' => 'submit', 'description' => 'Submit'),
        'SAVE' => array('value' => 'save', 'description' => 'Save'),
        'UPDATE' => array('value' => 'update', 'description' => 'Update'),
        'UPDATE_ONE_FIELD' => array('value' => 'update_one_field', 'description' => 'Update'),
        'DELETE' => array('value' => 'delete', 'description' => 'Delete'),
        'POST' => array('value' => 'post', 'description' => 'Post'),
        'GET' => array('value' => 'get', 'description' => 'Get'),
    );
}
