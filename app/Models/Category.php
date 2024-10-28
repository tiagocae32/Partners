<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';

    public $timestamps = true;


    protected $fillable = [
        'name',
        'category_group_id'
    ];
    public function group()
    {
        return $this->belongsTo(CategoryGroup::class, 'category_group_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }
}
