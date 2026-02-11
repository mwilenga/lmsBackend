<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends BaseModel
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = [];
    protected $guarded = [];


}