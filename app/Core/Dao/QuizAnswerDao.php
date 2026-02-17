<?php

namespace App\Core\Dao;

use App\Core\Model\QuizAnswer;
use App\Core\Util\StringUtil;

class QuizAnswerDao extends BaseDao
{
    protected $quizanswer;

    public function __construct(QuizAnswer $quizanswer)
    {
        parent::__construct($quizanswer);
        $this->quizanswer = $quizanswer;
    }

    private function bindData($quizanswer, $data)
    {
        if (!empty($data->active_user)) {
            $quizanswer->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $quizanswer->uuid = $data->uuid;
        if (!empty($data->user_id)) $quizanswer->user_id = $data->user_id;
        if (!empty($data->question_id)) $quizanswer->question_id = $data->question_id;
        if (!empty($data->answer)) $quizanswer->answer = $data->answer;

        if (!empty($data->company_id)) $quizanswer->company_id = $data->company_id;

        if (!empty($data->created_by)) $quizanswer->created_by = $data->created_by;
        if (!empty($data->updated_by)) $quizanswer->updated_by = $data->updated_by;

        return $quizanswer;
    }

    public function save($data, $firstOrCreate = false)
    {
        $quizanswer = new QuizAnswer();
        $quizanswer = $this->bindData($quizanswer, $data);
        $quizanswer = parent::save($quizanswer, $firstOrCreate);

        return $quizanswer;
    }

    public function update($data, $id)
    {
        $quizanswer = QuizAnswer::find($id);
        $quizanswer = $this->bindData($quizanswer, $data);
        $quizanswer = parent::save($quizanswer);

        return $quizanswer;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = QuizAnswer::query();

        if (!empty($id)) { $query->where('id', '=', $id); }
        if (!empty($title)) { $query->where('question_id', 'LIKE', StringUtil::helpLike($title)); }

        if (!empty($extra)) {
            if (!empty($extra['q'])) {
                $query->where(function ($query) use ($extra) {
                    $query->where('question_id', 'LIKE', StringUtil::helpLike($extra['q']))
                        ->orWhere('last_name', 'LIKE', StringUtil::helpLike($extra['q']));
                });
            }
            if (!empty($extra['user_id'])) { $query->where('user_id', '=', $extra['user_id']); }
            if (!empty($extra['question_id'])) { $query->where('question_id', '=', $extra['question_id']); }
            if (!empty($extra['uuid'])) { $query->where('uuid', '=', $extra['uuid']); }
            if (!empty($extra['description'])) { $query->where('description', '=', $extra['description']); }
            if (!empty($extra['company_id'])) { $query->where('company_id', '=', $extra['company_id']); }
        }

        $query->orderBy('id', 'DESC');
        if (!empty($limit) && $limit > 0) { $query->limit($limit); }

        if (!empty($extra)) {
            $listOfWith = [];
            if (!empty($extra['with_item'])) { $listOfWith = array_merge($listOfWith, ['item']); }
            if (!empty($listOfWith)) { $query->with($listOfWith); }
        }

        // exit(var_dump($this->getSql($query)));
        if ($first) { return $query->first();  } else if(isset($extra['paginate']) && $extra['paginate']) { return $query->paginate($extra['per_page']); } else { return $query->get(); }
    }

}