<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    public $timestamps = true;


    protected $fillable = [
        'name',
        'description',
        'image_url',
        'cta_url',
    ];


    public function categories(){
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }
}
