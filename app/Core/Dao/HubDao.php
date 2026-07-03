<?php

namespace App\Core\Dao;

use App\Core\Model\Hub;

class HubDao extends BaseDao
{
    protected $hub;

    public function __construct(Hub $hub)
    {
        parent::__construct($hub);
        $this->hub = $hub;
    }

    public function saveFromRegistration($data)
    {
        $hubData = $data->hub ?? null;
        if (is_array($hubData)) {
            $hubData = (object) $hubData;
        }

        $hub = new Hub();
        $hub->name = $hubData->name;
        $hub->title = $hubData->title;
        $hub->location = $hubData->location;
        $hub->phone = $hubData->phone;
        $hub->email = $hubData->email;
        $hub->active_user = $data->active_user;

        return parent::save($hub);
    }
}
