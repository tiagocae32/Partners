<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Repositories\CategoryRepository;

class CategoryController extends Controller {


    private CategoryRepository $categoryRepository;
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        $data = $this->categoryRepository->index();
        return ApiResponseClass::sendResponse(CategoryResource::collection($data),'');
    }

    public function store(StoreCategoryRequest $request)
    {
        startTransaction();
        $data = $request->validated();
        try{
             $product = $this->categoryRepository->store($data);
             commit();
             return ApiResponseClass::sendResponse(new CategoryResource($product),'Category Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function getProductsByCategories($ids)
    {
        $products = $this->categoryRepository->getProductsByCategories($ids);

        return response()->json($products);
    }

    public function destroy($id)
    {
        startTransaction();
        try{
             $categoryDelete = $this->categoryRepository->delete($id);
             commit();
             return ApiResponseClass::sendResponse($categoryDelete,'Category Delete Successful',204);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }


    public function getProductsByCategoryGroup($groupsId) {

        $products = $this->categoryRepository->getProductsByCategoryGroups($groupsId);

        return response()->json(['url' => $products]);
    }


}
