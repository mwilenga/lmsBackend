<?php

namespace App\Core\Dao;

use App\Core\Model\User;
use App\Core\Util\StringUtil;
use Illuminate\Support\Facades\Hash;


class UsersDao extends BaseDao
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    private function bindData($user, $data)
    {
        if (!empty($data->active_user)) {
            $user->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $user->uuid = $data->uuid;
        if (!empty($data->name)) $user->name = $data->name;
        if (!empty($data->phone)) $user->phone = $data->phone;
        if (!empty($data->email)) $user->email = $data->email;
        if (!empty($data->password)) $user->password = Hash::make($data->password);
        if (!empty($data->role)) $user->role = $data->role;

        if (!empty($data->company_id)) $user->company_id = $data->company_id;

        if (!empty($data->created_by)) $user->created_by = $data->created_by;
        if (!empty($data->updated_by)) $user->updated_by = $data->updated_by;

        return $user;
    }

    public function save($data, $firstOrCreate = false)
    {
        $user = new User();
        $user = $this->bindData($user, $data);
        $user = parent::save($user, $firstOrCreate);

        return $user;
    }

    public function update($data, $id)
    {
        $user = User::find($id);
        $user = $this->bindData($user, $data);
        $user = parent::save($user);

        return $user;
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = User::query();

        if (!empty($id)) { $query->where('id', '=', $id); }
        if (!empty($title)) { $query->where('name', 'LIKE', StringUtil::helpLike($title)); }

        if (!empty($extra)) {
            if (!empty($extra['q'])) {
                $query->where(function ($query) use ($extra) {
                    $query->where('name', 'LIKE', StringUtil::helpLike($extra['q']))
                        ->orWhere('email', 'LIKE', StringUtil::helpLike($extra['q']));
                });
            }
            if (!empty($extra['user_id'])) { $query->where('user_id', '=', $extra['user_id']); }
            if (!empty($extra['uuid'])) { $query->where('uuid', '=', $extra['uuid']); }
            if (!empty($extra['description'])) { $query->where('description', '=', $extra['description']); }
            if (!empty($extra['company_id'])) { $query->where('company_id', '=', $extra['company_id']); }
            if (!empty($extra['role'])) { $query->where('role', '=', $extra['role']); }
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