<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Module extends BaseModel
{
    use HasFactory;

    protected $table = 'module';
    protected $fillable = [];
    protected $guarded = [];

}