<?php

namespace App\Core\Dao;

use App\Core\Model\BaseModel;
use App\Core\Util\DateUtil;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;

class BaseDao
{
    protected $model;

    protected function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    protected function getSql($query)
    {
        return Str::replaceArray('?', $query->getBindings(), $query->toSql());
    }

    public function get($id)
    {
        if (!is_null($object = $this->model->where('id', '=', $id)->first())) {
            return $object;
        }

        return $this->model;
    }

    public function delete($id)
    {
        if (!is_null($object = $this->model->where('id', '=', $id)->first())) {
            $object->delete();
        } else {
            throw new Exception("Unable to remove this item");
        }
    }

    public function softDelete($activeUser, $id)
    {
        if (!is_null($object = $this->model->where('id', '=', $id)->first())) {
            $object->deleted_by = $activeUser;
            $object->deleted_at = DateUtil::now();
            $object->save();
        } else {
            throw new Exception("Unable to remove this item");
        }
    }

    public function save(BaseModel $genericObject, $firstOrCreate = false)
    {
        $active_user = $genericObject->active_user;
        if (empty($active_user)) {
            throw new Exception("Active User has not been SET!! " . $genericObject->getThisTable());
        }

        if (empty($genericObject->id)) {
            $genericObject->created_by = $active_user;
            $genericObject->created_at = Carbon::now();
        } else {
            $genericObject->updated_by = $active_user;
            $genericObject->updated_at = Carbon::now();
        }

        unset($genericObject->active_user);

        if ($firstOrCreate) {
            if (empty($this->model->getThisUniqueColumn())) {
                throw new Exception("Unique Column has not been SET!! " . $genericObject->getThisTable());
            }

            $lookfor = $this->model->getThisUniqueColumn();
            $listToLookFor = $genericObject->toArray();
            $genericObjectArray = collect($listToLookFor)->only($lookfor)->toArray();
            $nowGenericObject = $this->model->firstOrCreate($genericObjectArray);
            $attributes = array_keys($listToLookFor);
            foreach ($attributes as $key => $attribute) {
                $nowGenericObject->{$attribute} = $genericObject->{$attribute};
            }
            //$genericObject->id = $nowGenericObject->id;
            $nowGenericObject->save();
            $genericObject->id = $nowGenericObject->id;
        } else {
            $genericObject->save();
        }

        return $genericObject;
    }

    public function insert($listOfData)
    {
        return $this->model->insert($listOfData);
    }
}
