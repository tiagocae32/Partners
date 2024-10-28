<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Repositories\ProductRepository;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    
    private ProductRepository $productRepository;
    
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
   

    public function index()
    {
        $data = $this->productRepository->index();
        return ApiResponseClass::sendResponse(ProductResource::collection($data),'');
    }


    public function store(StoreProductRequest $request)
    {
        startTransaction();
        $data = $request->validated();
        try{
             $product = $this->productRepository->store($data);
             commit();
             return ApiResponseClass::sendResponse(new ProductResource($product),'Product Create Successful',201);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

   
    public function show($id)
    {
        try{
            $product = $this->productRepository->getById($id);
            return ApiResponseClass::sendResponse(new ProductResource($product),'Product Found',200);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        startTransaction();
        $data = $request->validated();
        try{
             $product = $this->productRepository->update($data,$id);
             commit();
             return ApiResponseClass::sendResponse(new ProductResource($product),'Product Edit Successful',200);

        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }

  
    public function destroy($id)
    {
        startTransaction();
        try{
             $productDelete = $this->productRepository->delete($id);
             commit();
             return ApiResponseClass::sendResponse($productDelete,'Product Delete Successful',204);
        }catch(\Exception $ex){
            return ApiResponseClass::rollback($ex);
        }
    }
}