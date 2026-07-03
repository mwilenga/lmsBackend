<?php

namespace App\Core\Dao;

use App\Core\Enum\CertificateApprovalStatus;
use App\Core\Model\Certificate;
use App\Core\Util\StringUtil;

class CertificateDao extends BaseDao
{
    protected $certificate;

    public function __construct(Certificate $certificate)
    {
        parent::__construct($certificate);
        $this->certificate = $certificate;
    }

    private function bindData($certificate, $data)
    {
        if (!empty($data->active_user)) {
            $certificate->active_user = $data->active_user;
        }

        if (!empty($data->uuid)) $certificate->uuid = $data->uuid;
        if (!empty($data->user_id)) $certificate->user_id = $data->user_id;
        if (!empty($data->path)) $certificate->path = $data->path;
        if (!empty($data->approval_status)) $certificate->approval_status = $data->approval_status;

        if (!empty($data->company_id)) $certificate->company_id = $data->company_id;

        if (!empty($data->created_by)) $certificate->created_by = $data->created_by;
        if (!empty($data->updated_by)) $certificate->updated_by = $data->updated_by;

        return $certificate;
    }

    public function save($data, $firstOrCreate = false)
    {
        $certificate = new Certificate();
        $certificate = $this->bindData($certificate, $data);
        if (empty($certificate->approval_status)) {
            $certificate->approval_status = CertificateApprovalStatus::get('PENDING/value');
        }
        $certificate = parent::save($certificate, $firstOrCreate);

        return $certificate->load('user');
    }

    public function update($data, $id)
    {
        $certificate = Certificate::find($id);
        $certificate = $this->bindData($certificate, $data);
        $certificate = parent::save($certificate);

        return $certificate->load('user');
    }

    public function approve($id, $data)
    {
        $certificate = Certificate::find($id);
        if (empty($certificate)) {
            return null;
        }

        $certificate->approval_status = CertificateApprovalStatus::get('APPROVED/value');
        $certificate->active_user = $data->active_user;

        return parent::save($certificate)->load('user');
    }

    public function one($id, $title, $extra = array())
    {
        return $this->search($id, $title, 1, $extra, true);
    }

    public function search($id, $title, $limit = 0, $extra = array(), $first = false)
    {
        $query = Certificate::query();

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
            if (!empty($extra['path'])) { $query->where('path', '=', $extra['path']); }
            if (!empty($extra['approval_status'])) { $query->where('approval_status', '=', $extra['approval_status']); }
        }

        $query->with('user');
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