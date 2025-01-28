<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvailableProductCollection;
use App\Http\Resources\AvailableProductResource;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Traits\ApiResponses;

class ProductController extends Controller
{
    use ApiResponses;


    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        return new AvailableProductCollection(
            Product::paginate()
        );
    }

    /**
     * Returns a product with its associated stock availability.
     * 
     * @param \App\Models\Product $product
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        return new AvailableProductResource($product);
    }
}
