<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\Model\User;

class Certificate extends BaseModel
{
    use HasFactory;

    protected $table = 'certificate';
    protected $fillable = [];
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}