<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryGroupsController;

Route::apiResource('/products',ProductController::class);

Route::apiResource('/categories',CategoryController::class);

Route::get('category_groups/{group}/products', [CategoryController::class, 'getProductsByCategoryGroup']);
Route::get('categories/{ids}/products', [CategoryController::class, 'getProductsByCategories']);

Route::get('categories_groups', [CategoryGroupsController::class, 'index']);
