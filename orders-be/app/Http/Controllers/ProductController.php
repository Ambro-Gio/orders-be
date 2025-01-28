<?php

namespace App\Http\Controllers;

use App\Http\Resources\AvailableProductCollection;
use App\Http\Resources\AvailableProductResource;
use App\Models\Product;
use App\Models\Stock;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ApiResponses;


    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
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

    /**
     * Creates a product and its associated stock availability
     * Default stock value is 0
     * 
     * @param App\Http\Requests\StoreProductRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request)
    {

        try {
            return DB::transaction(function () use ($request) {
                $createdProducts = [];
                foreach ($request->products as $data) {

                    $product = Product::create([
                        "name" => $data["name"],
                        "price" => $data["price"],
                    ]);
                    Stock::create([
                        "product_id" => $product->id,
                        "stock_quantity" => $data["quantity"] ?? 0,
                    ]);

                    $createdProducts[] = $product;
                }

                return new AvailableProductCollection($createdProducts);
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * updates a product and its associated stock availability
     * 
     * @param App\Http\Requests\UpdateProductRequest $request
     * @param \App\Models\Product $product
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, Product $product){

        try{
            return DB::transaction(function () use ($request, $product) {
                $product->update($request->only("name", "price"));
                $product->stock->update(["stock_quantity" => $request->quantity]);

                return $this->ok("OK");
            });
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}
