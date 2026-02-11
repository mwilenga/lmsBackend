<?php

namespace App\Core\Dao;

use App\Core\Model\Quiz;
use App\Core\Util\StringUtil;

class QuizDao extends BaseDao
{
    protected $quiz;

    public function __construct(Quiz $quiz)
    {
        parent::__construct($quiz);
        $this->quiz = $quiz;
    }

    private function bindData($quiz, $data)
    {
        if (!empty($data->active_user)) {
            $quiz->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $quiz->uuid = $data->uuid;
        if (!empty($data->module_id)) $quiz->module_id = $data->module_id;
        if (!empty($data->question)) $quiz->question = $data->question;
        if (!empty($data->options)) $quiz->options = $data->options;
        if (!empty($data->correct_option)) $quiz->correct_option = $data->correct_option;

        if (!empty($data->company_id)) $quiz->company_id = $data->company_id;

        if (!empty($data->created_by)) $quiz->created_by = $data->created_by;
        if (!empty($data->updated_by)) $quiz->updated_by = $data->updated_by;

        return $quiz;
    }

    public function save($data, $firstOrCreate = false)
    {
        $quiz = new Quiz();
        $quiz = $this->bindData($quiz, $data);
        $quiz = parent::save($quiz, $firstOrCreate);

        return $quiz;
    }

    public function update($data, $id)
    {
        $quiz = Quiz::find($id);
        $quiz = $this->bindData($quiz, $data);
        $quiz = parent::save($quiz);

        return $quiz;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = Quiz::query();

        if (!empty($id)) { $query->where('id', '=', $id); }
        if (!empty($title)) { $query->where('title', 'LIKE', StringUtil::helpLike($title)); }

        if (!empty($extra)) {
            if (!empty($extra['q'])) {
                $query->where(function ($query) use ($extra) {
                    $query->where('first_name', 'LIKE', StringUtil::helpLike($extra['q']))
                        ->orWhere('last_name', 'LIKE', StringUtil::helpLike($extra['q']));
                });
            }
            if (!empty($extra['user_id'])) { $query->where('user_id', '=', $extra['user_id']); }
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