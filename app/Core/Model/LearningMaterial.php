<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class LearningMaterial extends BaseModel
{
    use HasFactory;

    protected $table = 'learning_material';
    protected $fillable = [];
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

}