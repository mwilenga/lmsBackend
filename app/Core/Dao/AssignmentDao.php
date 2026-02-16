<?php

namespace App\Core\Dao;

use App\Core\Model\Assignment;
use App\Core\Util\StringUtil;

class AssignmentDao extends BaseDao
{
    protected $assignment;

    public function __construct(Assignment $assignment)
    {
        parent::__construct($assignment);
        $this->assignment = $assignment;
    }

    private function bindData($assignment, $data)
    {
        if (!empty($data->active_user)) {
            $assignment->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $assignment->uuid = $data->uuid;
        if (!empty($data->title)) $assignment->title = $data->title;
        if (!empty($data->description)) $assignment->description = $data->description;
        if (!empty($data->document_url)) $assignment->document_url = $data->document_url;
        if (!empty($data->assigned_user_id)) $assignment->assigned_user_id = $data->assigned_user_id;

        if (!empty($data->company_id)) $assignment->company_id = $data->company_id;

        if (!empty($data->created_by)) $assignment->created_by = $data->created_by;
        if (!empty($data->updated_by)) $assignment->updated_by = $data->updated_by;

        return $assignment;
    }

    public function save($data, $firstOrCreate = false)
    {
        $assignment = new Assignment();
        $assignment = $this->bindData($assignment, $data);
        $assignment = parent::save($assignment, $firstOrCreate);

        return $assignment;
    }

    public function update($data, $id)
    {
        $assignment = Assignment::find($id);
        $assignment = $this->bindData($assignment, $data);
        $assignment = parent::save($assignment);

        return $assignment;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = Assignment::query();

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