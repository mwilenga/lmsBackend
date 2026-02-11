<?php

namespace App\Core\Services;

use App\Core\Dao\UsersDao;

class UserService extends BaseService
{
    protected $usersDao;

    public function __construct(UsersDao $usersDao)
    {
        parent::__construct($usersDao);
        $this->usersDao = $usersDao;
    }

    public function validationRules()
    {
        return array(
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string'
        );
    }

    public function save($data)
    {
        return $this->usersDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->usersDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->usersDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->usersDao->search($id, $name, $limit, $extra);
    }
}