<?php

namespace App\Core\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hub extends BaseModel
{
    use HasFactory;

    protected $table = 'hub';
    protected $fillable = [];
    protected $guarded = [];
}
