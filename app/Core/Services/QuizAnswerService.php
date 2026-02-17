<?php

namespace App\Core\Services;

use App\Core\Dao\QuizAnswerDao;

class QuizAnswerService extends BaseService
{
    protected $quizanswerDao;

    public function __construct(QuizAnswerDao $quizanswerDao)
    {
        parent::__construct($quizanswerDao);
        $this->quizanswerDao = $quizanswerDao;
    }

    public function validationRules()
    {
        return array(
            'user_id' => 'required|integer',
            'answers' => 'required|array'
        );
    }

    public function save($data)
    {
        return $this->quizanswerDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->quizanswerDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->quizanswerDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->quizanswerDao->search($id, $name, $limit, $extra);
    }
}