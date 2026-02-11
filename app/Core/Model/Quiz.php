<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quiz extends BaseModel
{
    use HasFactory;

    protected $table = 'quiz';
    protected $fillable = [];
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

}