<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserModule extends BaseModel
{
    use HasFactory;

    protected $table = 'user_module';
    protected $fillable = [];
    protected $guarded = [];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}