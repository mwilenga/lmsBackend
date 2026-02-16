<?php

namespace App\Core\Services;

use App\Core\Dao\AssignmentDao;

class AssignmentService extends BaseService
{
    protected $assignmentDao;

    public function __construct(AssignmentDao $assignmentDao)
    {
        parent::__construct($assignmentDao);
        $this->assignmentDao = $assignmentDao;
    }

    public function validationRules()
    {
        return array(
            'title' => 'required|string',
            'assigned_user_id' => 'required|integer'
        );
    }

    public function save($data)
    {
        return $this->assignmentDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->assignmentDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->assignmentDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->assignmentDao->search($id, $name, $limit, $extra);
    }
}