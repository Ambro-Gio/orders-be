<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_list_routes()
    {
        $this->getJson('/api/products')->assertUnauthorized();
        $this->postJson('/api/products')->assertUnauthorized();
    }

    public function test_unauthenticated_user_cannot_access_detail_routes(){

        //creating a product
        
    }

}
