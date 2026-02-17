<?php

namespace App\Core\Dao;

use App\Core\Model\UserModule;
use App\Core\Util\StringUtil;

class UserModuleDao extends BaseDao
{
    protected $usermodule;

    public function __construct(UserModule $usermodule)
    {
        parent::__construct($usermodule);
        $this->usermodule = $usermodule;
    }

    private function bindData($usermodule, $data)
    {
        if (!empty($data->active_user)) {
            $usermodule->active_user = $data->active_user;
        }

        if (!empty($data->user_id)) $usermodule->user_id = $data->user_id;
        if (!empty($data->module_id)) $usermodule->module_id = $data->module_id;
        if (!empty($data->status)) $usermodule->status = $data->status;

        if (!empty($data->company_id)) $usermodule->company_id = $data->company_id;

        if (!empty($data->created_by)) $usermodule->created_by = $data->created_by;
        if (!empty($data->updated_by)) $usermodule->updated_by = $data->updated_by;

        return $usermodule;
    }

    public function save($data, $firstOrCreate = false)
    {
        $usermodule = new UserModule();
        $usermodule = $this->bindData($usermodule, $data);
        $usermodule = parent::save($usermodule, $firstOrCreate);

        return $usermodule;
    }

    public function update($data, $id)
    {
        $usermodule = UserModule::find($id);
        $usermodule = $this->bindData($usermodule, $data);
        $usermodule = parent::save($usermodule);

        return $usermodule;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = UserModule::query();

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
            if (!empty($extra['with_module'])) { $listOfWith = array_merge($listOfWith, ['module']); }
            if (!empty($listOfWith)) { $query->with($listOfWith); }
        }

        // exit(var_dump($this->getSql($query)));
        if ($first) { return $query->first();  } else if(isset($extra['paginate']) && $extra['paginate']) { return $query->paginate($extra['per_page']); } else { return $query->get(); }
    }

}