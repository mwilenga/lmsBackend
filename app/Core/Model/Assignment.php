<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\Model\User;

class Assignment extends BaseModel
{
    use HasFactory;

    protected $table = 'assignment';
    protected $fillable = [];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

}