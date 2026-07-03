<?php

namespace App\Core\Services;

use App\Core\Dao\HubDao;
use App\Core\Dao\UsersDao;
use App\Core\Enum\RegistrationType;

class UserService extends BaseService
{
    protected $usersDao;
    protected $hubDao;

    public function __construct(UsersDao $usersDao, HubDao $hubDao)
    {
        parent::__construct($usersDao);
        $this->usersDao = $usersDao;
        $this->hubDao = $hubDao;
    }

    public function validationRules()
    {
        return array_merge($this->baseUserRules(), $this->hubRules());
    }

    public function registrationValidationRules()
    {
        return array_merge($this->baseUserRules(), [
            'email' => 'required|email|unique:users,email',
        ], $this->hubRules());
    }

    private function baseUserRules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'phone' => 'required|string',
            'registration_type' => 'required|in:' . implode(',', RegistrationType::getValueList()),
            'nida' => 'nullable|string',
        ];
    }

    private function hubRules()
    {
        return [
            'hub.name' => 'required_if:registration_type,hub|string',
            'hub.title' => 'required_if:registration_type,hub|string',
            'hub.location' => 'required_if:registration_type,hub|string',
            'hub.phone' => 'required_if:registration_type,hub|string',
            'hub.email' => 'required_if:registration_type,hub|email',
        ];
    }

    public function save($data)
    {
        if (!empty($data->registration_type)
            && $data->registration_type === RegistrationType::get('HUB/value')) {
            $hub = $this->hubDao->saveFromRegistration($data);
            $data->hub_id = $hub->id;
        }

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
