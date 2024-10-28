<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\Product;

class CategoryRepository 
{

    public function index(){
        return Category::all();
    }

    public function store($data){
       $newCategory = new Category($data);
       $newCategory->save();
       return $newCategory;
    }

    public function delete($id){
        $category = Category::find($id); 
        if (!$category) {
           return response()->json(['message' => 'Categoria no encontrado'], 404);
        }
   
        $category->delete();
        return $category;
      }

    public function getProductsByCategories($ids) {
        $categoryIds = explode(',', trim($ids));
    
        if (empty($categoryIds)) {
            return response()->json(['error' => 'Se deben proporcionar IDs de categoría válidos.'], 400);
        }
    
        $products = Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })->with('categories')->get();
    
        $productsWithUrls = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'custom_url' => url("/api/products/{$product->id}"),
            ];
        });
    
        return $productsWithUrls;
    }
    
    public function getProductsByCategoryGroups($ids) {

        $groupIds = explode(',', $ids);
        $groups = CategoryGroup::with('categories')->whereIn('id', $groupIds)->get();
        
        $categoryIds = $groups->flatMap(function ($group) {
            return $group->categories->pluck('id');
        })->unique()->implode(',');
    
        $customUrl = url("/api/categories/{$categoryIds}/products");
        return $customUrl;
    }
    
    
    
}
