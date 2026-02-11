<?php

namespace App\Core\Services;

use App\Core\Dao\CertificateDao;

class CertificateService extends BaseService
{
    protected $certificateDao;

    public function __construct(CertificateDao $certificateDao)
    {
        parent::__construct($certificateDao);
        $this->certificateDao = $certificateDao;
    }

    public function validationRules()
    {
        return array(
            'user_id' => 'required|integer',
            'certificate' => 'required|string'
        );
    }

    public function save($data)
    {
        return $this->certificateDao->save($data);
    }

    public function update($data, $id)
    {
        return $this->certificateDao->update($data, $id);
    }

    public function one($id, $name, $extra = array())
    {
        return $this->certificateDao->one($id, $name, $extra);
    }

    public function search($id, $name, $limit = 0, $extra = array())
    {
        return $this->certificateDao->search($id, $name, $limit, $extra);
    }
}