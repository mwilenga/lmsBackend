<?php

namespace App\Core\Dao;

use App\Core\Model\LearningMaterial;
use App\Core\Util\StringUtil;

class LearningMaterialDao extends BaseDao
{
    protected $learningmaterial;

    public function __construct(LearningMaterial $learningmaterial)
    {
        parent::__construct($learningmaterial);
        $this->learningmaterial = $learningmaterial;
    }

    private function bindData($learningmaterial, $data)
    {
        if (!empty($data->active_user)) {
            $learningmaterial->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $learningmaterial->uuid = $data->uuid;
        if (!empty($data->module_id)) $learningmaterial->module_id = $data->module_id;
        if (!empty($data->title)) $learningmaterial->title = $data->title;
        if (!empty($data->description)) $learningmaterial->description = $data->description;
        if (!empty($data->type)) $learningmaterial->type = $data->type;
        if (!empty($data->file_path)) $learningmaterial->file_path = $data->file_path;

        if (!empty($data->company_id)) $learningmaterial->company_id = $data->company_id;

        if (!empty($data->created_by)) $learningmaterial->created_by = $data->created_by;
        if (!empty($data->updated_by)) $learningmaterial->updated_by = $data->updated_by;

        return $learningmaterial;
    }

    public function save($data, $firstOrCreate = false)
    {
        $learningmaterial = new LearningMaterial();
        $learningmaterial = $this->bindData($learningmaterial, $data);
        $learningmaterial = parent::save($learningmaterial, $firstOrCreate);

        return $learningmaterial;
    }

    public function update($data, $id)
    {
        $learningmaterial = LearningMaterial::find($id);
        $learningmaterial = $this->bindData($learningmaterial, $data);
        $learningmaterial = parent::save($learningmaterial);

        return $learningmaterial;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = LearningMaterial::query();

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
            if (!empty($extra['type'])) { $query->where('type', '=', $extra['type']); }
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