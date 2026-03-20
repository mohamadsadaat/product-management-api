<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Product;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
        use RefreshDatabase;
        public function test_authenticated_user_can_create_product(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/products', [
            'name' => 'Laptop',
            'description' => 'Gaming laptop',
            'price' => 1200,
            'sku' => 'LAPTOP-001',
            'stock' => 5,
            'status' => 'active',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ]);
    }

    public function test_user_can_update_product(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $product = Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Product',
            'price' => 100,
        ]);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 150,
            'sku' => 'UPDATED-001',
            'stock' => 10,
            'status' => 'active',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
            ])
            ->assertJsonFragment([
                'status' => true,
                'message' => 'Product updated successfully',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
            'price' => 150,
        ]);
    }

    public function test_user_cannot_update_another_users_product(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other Product',
        ]);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product',
            'price' => 150,
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_only_see_their_own_products(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Product::factory()->create([
            'user_id' => $user->id,
            'name' => 'My Product',
        ]);

        Product::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'Other Product',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'My Product'])
            ->assertJsonMissing(['name' => 'Other Product']);
    }

    public function test_user_cannot_delete_another_users_product(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $product = Product::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(403);
    }

    
}
