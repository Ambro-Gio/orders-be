<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Creates a product and its stock.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $productData): Product
    {
        return DB::transaction(function () use ($productData) {
            $product = Product::create([
                "name" => $productData["name"],
                "price" => $productData["price"],
            ]);

            // Create the stock entry
            $product->stock()->create([
                "stock_quantity" => $productData["quantity"] ?? 0,
            ]);

            return $product;
        });
    }
}
