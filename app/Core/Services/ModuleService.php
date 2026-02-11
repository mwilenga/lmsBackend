<?php

namespace App\Core\Services;

use App\Core\Dao\ModuleDao;

class ModuleService extends BaseService
{
    protected $moduleDao;

    public function __construct(ModuleDao $moduleDao)
    {
        parent::__construct($moduleDao);
        $this->moduleDao = $moduleDao;
    }

    public function validationRules()
    {
        return array(
            'name' => 'required|string',
            'description' => 'required|string',
            'code' => 'required|string'
        );
    }

    public function save($data)
    {
        return $this->moduleDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->moduleDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->moduleDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->moduleDao->search($id, $name, $limit, $extra);
    }
}