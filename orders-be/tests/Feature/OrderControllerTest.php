<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Services\OrderService;
use App\Services\ProductService;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{

    use RefreshDatabase;
    protected ProductService $productService;
    protected OrderService $orderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = app(ProductService::class);
        $this->orderService = app(OrderService::class);
    }

    public function testUnauthenticatedUserCannotAccessRoutes()
    {
        $this->getJson('/api/orders')->assertUnauthorized();
        $this->getJson('/api/orders/1')->assertUnauthorized();
        $this->postJson('/api/orders')->assertUnauthorized();
        $this->postJson("/api/orders/1/products")->assertUnauthorized();
        $this->putJson("/api/orders/1")->assertUnauthorized();
        $this->deleteJson("/api/orders/1")->assertUnauthorized();
        $this->deleteJson("/api/orders/1/products/1")->assertUnauthorized();
    }

    public function testIndexReturnsValidFormat()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["admin"]
        );

        $this->getJson("/api/orders")
            ->assertOk()
            ->assertJsonStructure([
                "data" => [
                    "*" => [
                        "ID",
                        "name",
                        "description",
                        "products" => [
                            '*' => [
                                "ID",
                                "name",
                                "price",
                                "quantity"
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function testShowReturnsValidFormat()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $order = Order::create([
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            "user_id" => auth()->id(),
        ]);

        $this->getJson("/api/orders/{$order->id}")
            ->assertOk()
            ->assertJsonStructure(
                [
                    "data" => [
                        "ID",
                        "name",
                        "description",
                        "products" => [
                            '*' => [
                                "ID",
                                "name",
                                "price",
                                "quantity"
                            ]
                        ]
                    ]
                ]
            );
    }

    public function testStoreIsSuccessful()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $product = $this->productService->createProduct(
            [
                "name" => fake()->word(),
                "price" => fake()->numberBetween(1, 100),
                "quantity" => fake()->numberBetween(1, 100)
            ]
        );

        $this->postJson(
            "/api/orders",
            [
                "name" => fake()->word(),
                "description" => fake()->sentence(),
                "products" => [
                    [
                        "ID" => $product->id,
                        "quantity" => 1,
                    ]
                ]
            ]
        )->assertCreated()
            ->assertJsonStructure([
                "data" => [
                    "ID",
                    "name",
                    "description",
                    "products" => [
                        '*' => [
                            "ID",
                            "name",
                            "price",
                            "quantity"
                        ]
                    ]
                ]
            ]);
    }

    public function testUpdateReturnsValidFormat()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $order = Order::create([
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            "user_id" => auth()->id(),
        ]);

        $this->putJson(
            "/api/orders/{$order->id}",
            [
                'name' => fake()->words(3, true),
                'description' => fake()->sentence(),
            ]
        )->assertOk();
    }

    public function testAddProductsIsSuccessful()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $product = $this->productService->createProduct(
            [
                "name" => fake()->word(),
                "price" => fake()->numberBetween(1, 100),
                "quantity" => fake()->numberBetween(1, 100)
            ]
        );

        $order = Order::create([
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            "user_id" => auth()->id(),
        ]);

        $this->postJson(
            "/api/orders/{$order->id}/products",
            [
                "products" => [
                    [
                        "ID" => $product->id,
                        "quantity" => 1,
                    ]
                ]
            ]
        )->assertOk()
            ->assertJsonStructure([
                "data" => [
                    "ID",
                    "name",
                    "description",
                    "products" => [
                        '*' => [
                            "ID",
                            "name",
                            "price",
                            "quantity"
                        ]
                    ]
                ]
            ]);
    }

    public function testDeleteIsSuccesful()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $order = Order::create([
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            "user_id" => auth()->id(),
        ]);

        $this->deleteJson(
            "/api/orders/{$order->id}"
        )->assertOk();
    }

    public function testDeleteProductIsSuccesful()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $order = Order::create([
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            "user_id" => auth()->id(),
        ]);

        $product = $this->productService->createProduct(
            [
                "name" => fake()->word(),
                "price" => fake()->numberBetween(1, 100),
                "quantity" => fake()->numberBetween(1, 100)
            ]
        );

        $order->products()->attach([
            $product->id => ['quantity' => 1]
        ]);

        $this->deleteJson("/api/orders/{$order->id}/products/{$product->id}")
            ->assertOk();
    }
}
