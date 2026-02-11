<?php

namespace App\Core\Services;

use App\Core\Dao\QuizDao;

class QuizService extends BaseService
{
    protected $quizDao;

    public function __construct(QuizDao $quizDao)
    {
        parent::__construct($quizDao);
        $this->quizDao = $quizDao;
    }

    public function validationRules()
    {
        return array(
            'module_id' => 'required|integer',
            'question' => 'required|string',
            'options' => 'required|array',
            'correct_option' => 'required|string'
        );
    }

    public function save($data)
    {
        return $this->quizDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->quizDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->quizDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->quizDao->search($id, $name, $limit, $extra);
    }
}