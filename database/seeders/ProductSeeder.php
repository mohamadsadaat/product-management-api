<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users to assign products to
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        // Create sample products
        $products = [
            [
                'name' => 'Laptop Pro',
                'sku' => 'LP-001',
                'description' => 'High-performance laptop with 16GB RAM and 512GB SSD',
                'price' => 1299.99,
                'stock' => 15,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Wireless Mouse',
                'sku' => 'WM-002',
                'description' => 'Ergonomic wireless mouse with long battery life',
                'price' => 29.99,
                'stock' => 50,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'USB-C Hub',
                'sku' => 'UH-003',
                'description' => '7-in-1 USB-C hub with HDMI, USB 3.0, and SD card reader',
                'price' => 49.99,
                'stock' => 25,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'MK-004',
                'description' => 'RGB mechanical keyboard with blue switches',
                'price' => 89.99,
                'stock' => 0,
                'status' => 'inactive',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Monitor 27"',
                'sku' => 'MO-005',
                'description' => '27-inch 4K monitor with HDR support',
                'price' => 399.99,
                'stock' => 8,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Webcam HD',
                'sku' => 'WC-006',
                'description' => '1080p HD webcam with auto-focus',
                'price' => 59.99,
                'stock' => 30,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Desk Lamp',
                'sku' => 'DL-007',
                'description' => 'LED desk lamp with adjustable brightness',
                'price' => 34.99,
                'stock' => 12,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
            [
                'name' => 'Phone Stand',
                'sku' => 'PS-008',
                'description' => 'Adjustable phone stand for desk use',
                'price' => 15.99,
                'stock' => 100,
                'status' => 'active',
                'user_id' => $users->random()->id,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create additional random products using factory if available
        // Product::factory(20)->create();
    }
}
