<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryGroups extends Model
{
    protected $table = 'category_groups';

    public $timestamps = true;


    protected $fillable = [
        'name',
    ];
}
