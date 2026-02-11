<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $uniqueColumn = [];

    public function getThisTable()
    {
        return $this->table;
    }

    public function getThisUniqueColumn()
    {
        return $this->uniqueColumn;
    }
}
