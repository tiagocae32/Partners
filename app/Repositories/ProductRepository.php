<?php

namespace App\Repositories;

use App\Interfaces\GenericAbmInterface;
use App\Models\Product;

class ProductRepository implements GenericAbmInterface
{
    public function index(){
        return Product::with('categories.group')->get();
    }

    public function getById($id){
       return Product::findOrFail($id);
    }

    public function store(array $data){
       
      $product = Product::create([
         'name' => $data['name'],
         'description' => $data['description']
      ]);
       $product->categories()->attach($data['categories']);

      if ($data['image_url']) {
         $imagePath = $data['image_url']->store("products/{$product->id}/image_url", 'public');
         $product->image_url = $imagePath;
      }

      if ($data['cta_url']) {
         $ctaPath = $data['cta_url']->store("products/{$product->id}/cta_url", 'public');
         $product->cta_url = $ctaPath;
      }

      $product->save();
      return $product;
    }

    public function update(array $data,$id){

       $product = Product::find($id);
       if (!$product) {
         return response()->json(['message' => 'Producto no encontrado'], 404);
       }
   
       $product->categories()->sync($data['categories']);
       $product->update();
       return $product;
   }
    
    public function delete($id){
      $product = Product::find($id); 
      if (!$product) {
         return response()->json(['message' => 'Producto no encontrado'], 404);
      }
 
      $product->categories()->detach();
      $product->delete();
      return $product;
    }
}
