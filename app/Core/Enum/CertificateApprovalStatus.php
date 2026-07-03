<?php

namespace App\Core\Enum;

class CertificateApprovalStatus extends BaseEnum
{
    protected static $enumList = [
        'APPROVED' => ['value' => 'approved', 'description' => 'Approved'],
        'PENDING' => ['value' => 'pending', 'description' => 'Pending'],
        'CANCELLED' => ['value' => 'cancelled', 'description' => 'Cancelled'],
        'FAILED' => ['value' => 'failed', 'description' => 'Failed'],
        'DENIED' => ['value' => 'denied', 'description' => 'Denied'],
    ];

    public static function getList()
    {
        $list = [];
        foreach (static::$enumList as $item) {
            $list[] = [
                'value' => $item['value'],
                'description' => $item['description'],
            ];
        }

        return $list;
    }
}
