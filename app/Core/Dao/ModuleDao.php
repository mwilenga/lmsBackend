<?php

namespace App\Core\Dao;

use App\Core\Model\Module;
use App\Core\Util\StringUtil;

class ModuleDao extends BaseDao
{
    protected $module;

    public function __construct(Module $module)
    {
        parent::__construct($module);
        $this->module = $module;
    }

    private function bindData($module, $data)
    {
        if (!empty($data->active_user)) {
            $module->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $module->uuid = $data->uuid;
        if (!empty($data->name)) $module->name = $data->name;
        if (!empty($data->description)) $module->description = $data->description;
        if (!empty($data->code)) $module->code = $data->code;

        if (!empty($data->company_id)) $module->company_id = $data->company_id;

        if (!empty($data->created_by)) $module->created_by = $data->created_by;
        if (!empty($data->updated_by)) $module->updated_by = $data->updated_by;

        return $module;
    }

    public function save($data, $firstOrCreate = false)
    {
        $module = new Module();
        $module = $this->bindData($module, $data);
        $module = parent::save($module, $firstOrCreate);

        return $module;
    }

    public function update($data, $id)
    {
        $module = Module::find($id);
        $module = $this->bindData($module, $data);
        $module = parent::save($module);

        return $module;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = Module::query();

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