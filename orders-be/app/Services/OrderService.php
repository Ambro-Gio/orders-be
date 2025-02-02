<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Stock;

class OrderService
{
    /**
     * Creates a product and its stock.
     *
     * @param array $data
     * @return Product
     */
    public function addProductsToOrder(Order $order, array $productsToAdd): Order
    {
        return DB::transaction(function () use ($order, $productsToAdd) {

            $products = collect($productsToAdd)->mapWithKeys(function ($product) use ($order) {

                // Check if the product exists in the order
                $pivotRow = $order->products()
                    ->where('product_id', $product["ID"])
                    ->first();

                if ($pivotRow) {
                    throw new \Exception("Product ID: {$product["ID"]} is already in the order");
                }

                // Trying to decrement stock quantity by the requested quantiy -> failure means insufficient stock, transaction is aborted.
                // This ensures correct functionality against race conditions.
                $updatedRows = Stock::where('product_id', $product['ID'])
                    ->where('stock_quantity', '>=', $product['quantity'])
                    ->decrement('stock_quantity', $product['quantity']);

                if ($updatedRows === 0) {
                    throw new \Exception("Insufficient stock for product ID: {$product['ID']}");
                }

                return [$product['ID'] => ['quantity' => $product['quantity']]];
            });
            $order->products()->attach($products);
            return $order;
        });
    }
}
