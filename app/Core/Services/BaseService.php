<?php

namespace App\Core\Services;

use App\Core\Dao\BaseDao;
use Illuminate\Support\Facades\DB;


class BaseService
{
    private $baseDao;

    public function __construct(BaseDao $baseDao)
    {
        $this->baseDao = $baseDao;
    }

    public function transaction($okFunction)
    {
        return DB::transaction(function () use ($okFunction) {
            return $okFunction();
        }, 1);
    }

    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    protected function rollBack()
    {
        DB::rollBack();
    }

    public function commit()
    {
        DB::commit();
    }

    public function get($id)
    {
        return $this->baseDao->get($id);
    }

    public function delete($id)
    {
        return $this->baseDao->delete($id);
    }

    public function softDelete($activeUser, $id)
    {
        return $this->baseDao->softDelete($activeUser, $id);
    }

    public function insert($listOfData)
    {
        return $this->baseDao->insert($listOfData);
    }
}
