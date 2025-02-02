<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\ProductService;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;
    protected ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = app(ProductService::class);
    }

    public function testUnauthenticatedUserCannotAccessRoutes()
    {
        $this->getJson('/api/products')->assertUnauthorized();
        $this->getJson('/api/products/1')->assertUnauthorized();
        $this->postJson('/api/products')->assertUnauthorized();
        $this->putJson("/api/products/1")->assertUnauthorized();
    }

    public function testUserRoleCanAccessReadRoutes()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );

        $product = $this->productService->createProduct(
            [
                "name" => "test",
                "price" => 2,
                "quantity" => 3
            ]
        );


        $this->getJson("/api/products")->assertOk();
        $this->getJson("/api/products/{$product->id}")->assertOk();
    }

    public function testUserRoleCannotAccessWriteRoutes()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["user"]
        );
        $product = $this->productService->createProduct(
            [
                "name" => "test",
                "price" => 2,
                "quantity" => 3
            ]
        );
        $this->postJson("/api/products")->assertForbidden();
        $this->putJson("/api/products/{$product->id}")->assertForbidden();
    }

    public function testIndexReturnsValidFormat()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["admin"]
        );

        $product = $this->productService->createProduct(
            [
                "name" => "test",
                "price" => 2,
                "quantity" => 3
            ]
        );

        $this->getJson("/api/products")
            ->assertOk()
            ->assertJsonStructure(

                [
                    "data" => [
                        '*' => [
                            "ID",
                            "name",
                            "price",
                            "availableQuantity",
                        ]
                    ]
                ]
            );
    }

    public function testShowReturnsValidFormat()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["admin"]
        );

        $product = $this->productService->createProduct(
            [
                "name" => "test",
                "price" => 2,
                "quantity" => 3
            ]
        );

        $this->getJson("/api/products/{$product->id}")
            ->assertOk()
            ->assertJsonStructure(

                [
                    "data" => [
                        "ID",
                        "name",
                        "price",
                        "availableQuantity",
                    ]
                ]
            );
    }

    public function testProductIsCreatedSuccesfully()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["admin"]
        );

        $product =
            [
                "name" => "test",
                "price" => 2,
                "quantity" => 3
            ];

        $this->postJson(
            "/api/products",
            [
                "products" => [$product]
            ]
        )
            ->assertOk()
            ->assertJsonStructure(
                [
                    "data" => [
                        '*' => [
                            "ID",
                            "name",
                            "price",
                            "availableQuantity",
                        ]
                    ]
                ]
            );
    }

    public function testProductIsUpdatedSuccesfully()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ["admin"]
        );

        $product = $this->productService->createProduct(
            [
                "name" => fake()->word(),
                "price" => fake()->numberBetween(1,100),
                "quantity" => fake()->numberBetween(1,100)
            ]
        );

        $updatedProduct = [
                "name" => fake()->word(),
                "price" => fake()->numberBetween(1, 100),
                "quantity" => fake()->numberBetween(1,100)
        ];

        $this->putJson("/api/products/{$product->id}", $updatedProduct)
            ->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $updatedProduct["name"],
            'price' => $updatedProduct["price"],
        ]);
        $this->assertDatabaseHas('stock', [
            'product_id' => $product->id,
            'stock_quantity' => $updatedProduct["quantity"],
        ]);
    }
}
