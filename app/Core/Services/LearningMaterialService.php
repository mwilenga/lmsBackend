<?php

namespace App\Core\Services;

use App\Core\Dao\LearningMaterialDao;

class LearningMaterialService extends BaseService
{
    protected $learningmaterialDao;

    public function __construct(LearningMaterialDao $learningmaterialDao)
    {
        parent::__construct($learningmaterialDao);
        $this->learningmaterialDao = $learningmaterialDao;
    }

    public function validationRules()
    {
        return array(
            'title' => 'required|string',
            'module_id' => 'required|integer'
        );
    }

    public function save($data)
    {
        return $this->learningmaterialDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->learningmaterialDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->learningmaterialDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->learningmaterialDao->search($id, $name, $limit, $extra);
    }
}