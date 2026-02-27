<?php

namespace App\Core\Services;

use App\Core\Dao\UserModuleDao;

class UserModuleService extends BaseService
{
    protected $usermoduleDao;

    public function __construct(UserModuleDao $usermoduleDao)
    {
        parent::__construct($usermoduleDao);
        $this->usermoduleDao = $usermoduleDao;
    }

    public function validationRules()
    {
        return array(
            'user_id' => 'required|integer',
            'module_id' => 'required|integer'
        );
    }

    public function save($data)
    {
        return $this->usermoduleDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->usermoduleDao->update($data, $id);
    }

    public function updateUserModuleStatus($data, $userId)
    {
        return $this->usermoduleDao->updateUserModuleStatus($data, $userId);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->usermoduleDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->usermoduleDao->search($id, $name, $limit, $extra);
    }
}