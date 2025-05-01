<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Symfony\Component\Translation\t;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
describe("POST /api/products", function() {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->tax1 = \App\Models\Tax::create([
            'name' => 'PPN',
            'rate' => 10
        ]);

        $this->tax2 = \App\Models\Tax::create([
            'name' => 'PPh',
            'rate' => 2
        ]);

    });

    it('can create product with default image and taxes if none uploaded', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'name' => 'Default Image Product',
            'purchase_price' => 15000,
            'selling_price' => 20000,
            'stock' => 50,
            'stock_alert' => 5,
            'unit_id' => $this->unit->id,
            'description' => 'No image uploaded',
            'brand' => 'BrandZ',
            'discount' => 5,
            'weight' => 1.0,
            'volume' => 0.5,
            'category_id' => $this->category->id,
            'taxes' => [
                ['tax_id' => $this->tax1->id],
                ['tax_id' => $this->tax2->id],
            ]
        ]);

        $response->assertCreated();

        $productId = $response->json('data.id');

        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'name' => 'Default Image Product',
            'images' => 'product-images/product-default.jpg',
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $productId,
            'tax_id' => $this->tax1->id,
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $productId,
            'tax_id' => $this->tax2->id,
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $productId,
            'tax_id' => $this->tax1->id,
            'amount' => 1500.00,
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $productId,
            'tax_id' => $this->tax2->id,
            'amount' => 300.00,
        ]);
    });



    it('can create product with uploaded image', function () {
        Storage::fake('public');

        Storage::disk('public')->put(
            'product-images/product-default.jpg',
            file_get_contents(public_path('product-images/product-default.jpg'))
        );

        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'name' => 'With Image',
            'purchase_price' => 10000,
            'selling_price' => 12000,
            'stock' => 10,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'images' => $image,
            'taxes' => [
                ['tax_id' => $this->tax1->id],
                ['tax_id' => $this->tax2->id],
            ]
        ]);
        $response->assertCreated();
        $data = $response->json('data');

        Storage::disk('public')->assertExists(Str::after($data['images'], 'storage/'));

        $this->assertDatabaseHas('products', [
            'name' => 'With Image',
        ]);
    });


    it('can create product with provided SKU and barcode', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'name' => 'Custom Identifiers',
            'sku' => 'SKU123',
            'barcode' => 'BARCODE123',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'stock' => 10,
            'taxes' => [
                ['tax_id' => $this->tax1->id],
                ['tax_id' => $this->tax2->id],
            ]
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('products', [
            'name' => 'Custom Identifiers',
            'sku' => 'SKU123',
            'barcode' => 'BARCODE123',
        ]);
    });

    it('fails to create product with missing required fields', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/products', []);

        $response->assertStatus(400);
        expect($response->json("errors.message.name.0"))->toBe("The name field is required.");
        expect($response->json("errors.message.purchase_price.0"))->toBe("The purchase price field is required.");
    });

    it('auto-generates SKU and barcode if not provided', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'name' => 'Auto Gen Product',
            'purchase_price' => 5000,
            'selling_price' => 7500,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'stock' => 10,
            'taxes' => [
                ['tax_id' => $this->tax1->id],
                ['tax_id' => $this->tax2->id],
            ]
        ]);

        $response->assertCreated();
        $data = $response->json('data');

        expect($data['sku'])->toBeString()->toMatch('/^[A-Z]+-[A-Z]+-[A-Z0-9]{4}$/');
        expect($data['barcode'])->toBeString()->toHaveLength(13);
    });

    it('fails if image is not a valid image file', function () {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('not-an-image.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'name' => 'Invalid Image',
            'purchase_price' => 5000,
            'selling_price' => 6000,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'stock' => 10,
            'images' => $file,
            'taxes' => [
                ['tax_id' => $this->tax1->id],
                ['tax_id' => $this->tax2->id],
            ]
        ]);


        $response->assertStatus(400);
        expect($response->json('errors.message.images.0'))->toBe('The images field must be an image.');
        expect($response->json('errors.message.images.1'))->toBe('The images field must be a file of type: jpg, jpeg, png.');
    });


    it('stores uploaded image with UUID name', function () {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->actingAs($this->admin)->postJson('/api/products', [
            'name' => 'UUID Image Test',
            'purchase_price' => 10000,
            'selling_price' => 12000,
            'stock' => 10,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'images' => $image,
            'taxes' => [
                ['tax_id' => $this->tax1->id],
                ['tax_id' => $this->tax2->id],
            ]
        ]);

        $response->assertCreated();
        $imagePath = Str::after($response->json('data.images'), 'storage/');

        expect($imagePath)->not->toContain('product.jpg');

        $fileName = basename($imagePath);
        $nameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);

        expect(Str::isUuid($nameWithoutExt))->toBeTrue();

        Storage::disk('public')->assertExists($imagePath);
    });


});

describe("GET /api/products/{id}", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

    });

    it('can retrieve a product with barcode image', function () {
        $product = \App\Models\Product::create([
            'name' => 'Sample Product',
            'sku' => 'SKU001',
            'barcode' => '1234567890123',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 10,
            'unit_id' => $this->unit->id,
            'category_id' => $this->category->id,
            'images' => 'product-images/product-default.jpg',
        ]);

        $response = $this->actingAs($this->admin)->getJson("/api/products/{$product->id}");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'sku', 'barcode', 'description', 'brand',
                'purchase_price', 'selling_price', 'stock', 'stock_alert',
                'discount', 'weight', 'volume', 'images', 'unit', 'category',
            ],
            "barcode_image",
            'message',
            'statusCode',
        ]);

        expect($response->json('barcode_image'))->toStartWith('data:image/png;base64,');
    });

    it('returns 404 if product not found', function () {
        $response = $this->actingAs($this->admin)->getJson("/api/products/999");

        $response->assertNotFound();
        $response->assertJson([
            'errors' => [
                'message' => 'Product not found',
            ],
        ]);
    });


});

describe("PATCH /api/products/{id}", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->product = Product::create([
            'name' => 'Original Product',
            'sku' => 'ORI-PROD',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 100,
            'stock_alert' => 10,
            'unit_id' => $this->unit->id,
            'description' => 'desc',
            'brand' => 'BrandA',
            'discount' => 0,
            'weight' => 1,
            'volume' => 0.5,
            'category_id' => $this->category->id,
            'barcode' => '0123456789012',
            'images' => 'product-images/product-default.jpg',
        ]);
        $this->tax1 = Tax::create(['name' => 'PPN', 'rate' => 10]);
        $this->tax2 = Tax::create(['name' => 'PPh', 'rate' => 2]);

        $this->product->taxes()->attach([
            $this->tax1->id => ['amount' => 1000],
            $this->tax2->id => ['amount' => 200],
        ]);
    });

    it('updates tax amounts when purchase price changes', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $newPurchasePrice = 20000;

        $response = $this->patchJson("/api/products/{$this->product->id}", [
            'purchase_price' => $newPurchasePrice,
        ]);
        $response->assertOk();

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax1->id,
            'amount' => 2000.00,
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax2->id,
            'amount' => 400.00,
        ]);
    });
    it('syncs taxes and updates amount when taxes are changed', function () {
        $newTax = Tax::create(['name' => 'Pajak Baru', 'rate' => 5]);

        $response = $this->actingAs($this->admin)->patchJson("/api/products/{$this->product->id}", [
            'taxes' => [$newTax->id],
        ]);

        $productId = $this->product->id;

        $response->assertOk();

        $this->assertDatabaseMissing('product_taxes', [
            'product_id' => $productId,
            'tax_id' => $this->tax1->id,
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $productId,
            'tax_id' => $newTax->id,
            'amount' => 500.00,
        ]);


    });

    it('syncs taxes and calculates correct amount using updated purchase price', function () {
        $newTax = Tax::create(['name' => 'Pajak Baru', 'rate' => 10]);

        $response = $this->actingAs($this->admin)->patchJson("/api/products/{$this->product->id}", [
            'purchase_price' => 30000,
            'taxes' => [$newTax->id],
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax1->id,
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $newTax->id,
            'amount' => 3000.00,
        ]);
    });
    it('does not change taxes or amount when neither purchase_price nor taxes are updated', function () {
        $response = $this->actingAs($this->admin)->patchJson("/api/products/{$this->product->id}", [
            'name' => 'Produk Baru',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax1->id,
            'amount' => 1000.00,
        ]);
    });
    it('removes all taxes when empty array is provided', function () {
        $response = $this->actingAs($this->admin)->patchJson("/api/products/{$this->product->id}", [
            'taxes' => [],
        ]);

        $response->assertOk();

        $this->assertDatabaseMissing('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax1->id,
        ]);

        $this->assertDatabaseMissing('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax2->id,
        ]);
    });

    it('fails with 400 when provided tax_id does not exist', function () {
        $response = $this->actingAs($this->admin)->patchJson("/api/products/{$this->product->id}", [
            'taxes' => [999],
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'taxes.0' => ['The selected taxes.0 is invalid.']
        ]);


    });



});

describe("GET /api/products", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->tax1 = \App\Models\Tax::create([
            'name' => 'PPN',
            'rate' => 10,
        ]);

        $this->tax2 = \App\Models\Tax::create([
            'name' => 'Service Tax',
            'rate' => 2,
        ]);


        for ($i = 1; $i <= 50; $i++) {
            $product = \App\Models\Product::create([
                'name' => "Product $i",
                'sku' => "PRD-$i",
                'barcode' => "BRCD-$i",
                'description' => "Deskripsi Product $i",
                'brand' => "Brand $i",
                'purchase_price' => 10000 + $i,
                'selling_price' => 15000 + $i,
                'stock' => 10 + $i,
                'stock_alert' => 5,
                'discount' => 0,
                'weight' => 1,
                'volume' => 1,
                'unit_id' => $this->unit->id,
                'category_id' => $this->category->id,
                'images' => 'product-images/product-default.jpg',
            ]);

            $taxes = [
                $this->tax1->id => ['amount' => $product->purchase_price * ($this->tax1->rate / 100)],
                $this->tax2->id => ['amount' => $product->purchase_price * ($this->tax2->rate / 100)],
            ];
            $product->taxes()->sync($taxes);
        }


    });

    it('returns paginated list of products', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/products');
        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
            'message',
            'statusCode',
        ]);
        expect(count($response->json('data')))->toBeLessThanOrEqual(10);
    });

    it('returns paginated list of products with page and per page', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/products?page=2&per_page=5');
        $response->assertOk();

        $response->assertJsonStructure([
            'data',
            'links',
            'meta',
            'message',
            'statusCode',
        ]);

        expect(count($response->json('data')))->toBeLessThanOrEqual(5);

        expect($response->json('meta.current_page'))->toBe(2);
        expect($response->json('meta.per_page'))->toBe(5);
    });

    it('can filter products by name', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/products?name=Product 1');
        $response->assertOk();

        $response->assertJsonFragment([
            'name' => 'Product 1',
        ]);
    });

    it('can filter products by category_id', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/products?category_id=' . $this->category->id);
        $response->assertOk();

        foreach ($response->json('data') as $product) {
            expect($product['category_id'])->toEqual($this->category->id);
        }
    });

    it('can filter products by purchase price range', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/products?min_purchase_price=1000&max_purchase_price=20000');
        $response->assertOk();

        foreach ($response->json('data') as $product) {
            expect($product['purchase_price'])->toBeGreaterThanOrEqual(1000);
            expect($product['purchase_price'])->toBeLessThanOrEqual(20000);
        }
    });

});

describe("DELETE /api/products/{id}", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->product = Product::create([
            'name' => 'Original Product',
            'sku' => 'ORI-PROD',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 100,
            'stock_alert' => 10,
            'unit_id' => $this->unit->id,
            'description' => 'desc',
            'brand' => 'BrandA',
            'discount' => 0,
            'weight' => 1,
            'volume' => 0.5,
            'category_id' => $this->category->id,
            'barcode' => '0123456789012',
            'images' => 'product-images/product-default.jpg',
        ]);
        $this->tax1 = Tax::create(['name' => 'PPN', 'rate' => 10]);
        $this->tax2 = Tax::create(['name' => 'PPh', 'rate' => 2]);

        $this->product->taxes()->attach([
            $this->tax1->id => ['amount' => 1000],
            $this->tax2->id => ['amount' => 200],
        ]);
    });
    it('can soft delete a product and keep tax relations', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/products/{$this->product->id}");

        expect($response->json("data.message"))->toBe("Product deleted successfully");

        $this->assertSoftDeleted('products', [
            'id' => $this->product->id
        ]);

        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax1->id,
            'amount' => 1000,
        ]);
        $this->assertDatabaseHas('product_taxes', [
            'product_id' => $this->product->id,
            'tax_id' => $this->tax2->id,
            'amount' => 200,
        ]);
    });

    it('returns 404 if product not found', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/products/9999");

        $response->assertNotFound()
            ->assertJson([
                'errors' => [
                    'message' => 'Product not found'
                ]
            ]);
    });

    it("Should be fail delete because there is a relationship with purchase ", function () {


        $this->purchaseUser = \App\Models\User::query()->create([
            "name" => "supplier",
            "email" => "supplier@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(5)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->originalProducts = $this->products->take(3);

        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->purchaseUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();


        $response = $this->deleteJson("/api/products/{$this->originalProducts[0]->id}");
        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'message' => 'Cannot delete because it is still associated with one or more purchases.'
            ]
        ]);
    });



});

describe("PATCH /api/products/{id}/restore", function () {
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);


        $this->product = Product::create([
            'name' => 'Original Product',
            'sku' => 'ORI-PROD',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 100,
            'stock_alert' => 10,
            'unit_id' => $this->unit->id,
            'description' => 'desc',
            'brand' => 'BrandA',
            'discount' => 0,
            'weight' => 1,
            'volume' => 0.5,
            'category_id' => $this->category->id,
            'barcode' => '0123456789012',
            'images' => 'product-images/product-default.jpg',
        ]);
        $this->tax1 = Tax::create(['name' => 'PPN', 'rate' => 10]);
        $this->tax2 = Tax::create(['name' => 'PPh', 'rate' => 2]);

        $this->product->taxes()->attach([
            $this->tax1->id => ['amount' => 1000],
            $this->tax2->id => ['amount' => 200],
        ]);
    });

    it('can restore a soft-deleted product', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->product->delete();

        $this->assertSoftDeleted('products', [
            'id' => $this->product->id,
        ]);

        $response = $this->patchJson("/api/products/{$this->product->id}/restore");

        $response->assertOk()
            ->assertJson([
                'message' => 'Product restored successfully',
                'statusCode' => 200,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'deleted_at' => null,
        ]);
    });

    it('cannot restore product that is not soft-deleted', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->assertNull($this->product->deleted_at);

        $response = $this->patchJson("/api/products/{$this->product->id}/restore");

        expect($response->json("errors.message"))->tobe("Cannot restore, product is not deleted");
    });

    it('returns 404 if product not found including trashed', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $nonExistentId = 99999;

        $response = $this->patchJson("/api/products/{$nonExistentId}/restore");

        $response->assertNotFound();
        expect($response->json("errors.message"))->toBe("Product not found");
    });

});


describe("DELETE /api/products/{id}/force", function (){
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);

        $this->product = Product::create([
            'name' => 'Original Product',
            'sku' => 'ORI-PROD',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 100,
            'stock_alert' => 10,
            'unit_id' => $this->unit->id,
            'description' => 'desc',
            'brand' => 'BrandA',
            'discount' => 0,
            'weight' => 1,
            'volume' => 0.5,
            'category_id' => $this->category->id,
            'barcode' => '0123456789012',
            'images' => 'product-images/product-default.jpg',
        ]);
        $this->tax1 = Tax::create(['name' => 'PPN', 'rate' => 10]);
        $this->tax2 = Tax::create(['name' => 'PPh', 'rate' => 2]);

        $this->product->taxes()->attach([
            $this->tax1->id => ['amount' => 1000],
            $this->tax2->id => ['amount' => 200],
        ]);
    });

    it('successfully hard deletes a soft-deleted product', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->product->delete();

        $response = $this->deleteJson("/api/products/{$this->product->id}/force");

        $response->assertOk();
        expect($response->json("data.message"))->toBe("Product deleted successfully");

        $this->assertDatabaseMissing('products', [
            'id' => $this->product->id,
        ]);

        $this->assertDatabaseMissing('product_taxes', [
            'product_id' => $this->product->id,
        ]);
    });

    it('fails to hard delete product that is not soft-deleted', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/products/{$this->product->id}/force");

        $response->assertStatus(400);
        expect($response->json("errors.message"))->toBe("Cannot hard delete, product is not soft deleted");

        $this->assertDatabaseHas('products', [
            'id' => $this->product->id,
            'deleted_at' => null,
        ]);
    });

    it('returns 404 when product not found (even withTrashed)', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $response = $this->deleteJson("/api/products/999999/force");

        $response->assertStatus(404);
    });
    it('hard deletes product without taxes', function () {
        \Laravel\Sanctum\Sanctum::actingAs($this->admin);

        $this->product->taxes()->detach();
        $this->product->delete();

        $response = $this->deleteJson("/api/products/{$this->product->id}/force");

        $response->assertOk();

        $this->assertDatabaseMissing('products', [
            'id' => $this->product->id,
        ]);
    });

    it("Should be fail delete because there is a relationship with purchase ", function () {


        $this->purchaseUser = \App\Models\User::query()->create([
            "name" => "supplier",
            "email" => "supplier@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = Unit::factory()->create();
        $this->category = Category::factory()->create();
        $this->supplier = Supplier::factory()->create();

        $this->products = Product::factory()
            ->count(1)
            ->for($this->category)
            ->for($this->unit)
            ->create();

        $this->product = $this->products->first();
        $this->originalProducts = collect([$this->product]);


        $this->taxes = Tax::factory()->count(3)->create();
        $this->originalTaxes = $this->taxes->take(2);

        $this->payload = [
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'total_amount' => 500000,
            'total_discount' => 50000,
            'shipping_amount' => 20000,
            'status' => 'confirmed',
            'payment_status' => 'partially_paid',
            'due_date' => now()->addDays(10)->toDateString(),
            'estimated_arrival_date' => now()->addDays(5)->toDateString(),
            'supplier_id' => $this->supplier->id,
            'purchase_details' => $this->originalProducts->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_price' => 100000,
                    'sub_total' => 500000,
                    'note' => 'test note'
                ];
            })->toArray(),
            'purchase_payments' => [
                [
                    'payment_date' => now()->toDateString(),
                    'amount' => 300000,
                    'due_date' => now()->addDays(10)->toDateString(),
                    'payment_method' => 'bank_transfer',
                    'status' => 'paid',
                    'note' => 'first payment'
                ]
            ],
            'taxes' => $this->originalTaxes->map(function ($tax) {
                return [
                    'tax_id' => $tax->id
                ];
            })->toArray()
        ];

        $response = $this->actingAs($this->purchaseUser)->postJson('/api/purchases', $this->payload);
        $responseData = $response->json();
        $this->purchaseId = $responseData['data']['id'];
        $this->originalPurchase = \App\Models\Purchase::find($this->purchaseId);
        $this->originalDetailIds = $this->originalPurchase->details->pluck('id')->toArray();
        $this->originalPaymentIds = $this->originalPurchase->payments->pluck('id')->toArray();

        $this->product->delete();
        $response = $this->deleteJson("/api/products/{$this->product->id}/force");;
        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'message' => 'Cannot delete because it is still associated with one or more purchases.'
            ]
        ]);
    });

});

describe("GET /api/products/trashed", function(){
    beforeEach(function() {
        $this->adminRole = \App\Models\Role::query()->create([
            "name" => "Admin"
        ]);

        $this->purchaseRole = \App\Models\Role::query()->create([
            "name" => "Purchasing"
        ]);

        $this->admin = \App\Models\User::query()->create([
            "name" => "admin",
            "email" => "admin@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->adminRole->id
        ]);

        $this->purchase = \App\Models\User::query()->create([
            "name" => "purchase",
            "email" => "purchase@example.com",
            "password" => \Illuminate\Support\Facades\Hash::make("rahasia12345"),
            "role_id" => $this->purchaseRole->id
        ]);

        $this->unit = \App\Models\Unit::create([
            "name" => "kilo gram",
            "code" => "kg",
        ]);

        $this->category = \App\Models\Category::create([
            'name' => 'Sample Category',
        ]);


        $this->tax1 = \App\Models\Tax::create([
            'name' => 'PPN',
            'rate' => 10,
        ]);

        $this->tax2 = \App\Models\Tax::create([
            'name' => 'Service Tax',
            'rate' => 2,
        ]);


        for ($i = 1; $i <= 50; $i++) {
            $product = \App\Models\Product::create([
                'name' => "Product $i",
                'sku' => "PRD-$i",
                'barcode' => "BRCD-$i",
                'description' => "Deskripsi Product $i",
                'brand' => "Brand $i",
                'purchase_price' => 10000 + $i,
                'selling_price' => 15000 + $i,
                'stock' => 10 + $i,
                'stock_alert' => 5,
                'discount' => 0,
                'weight' => 1,
                'volume' => 1,
                'unit_id' => $this->unit->id,
                'category_id' => $this->category->id,
                'images' => 'product-images/product-default.jpg',
            ]);

            $taxes = [
                $this->tax1->id => ['amount' => $product->purchase_price * ($this->tax1->rate / 100)],
                $this->tax2->id => ['amount' => $product->purchase_price * ($this->tax2->rate / 100)],
            ];
            $product->taxes()->sync($taxes);
        }


    });

    it("should return paginated list of soft deleted products", function () {

        $trashedProducts = \App\Models\Product::limit(10)->get();
        foreach ($trashedProducts as $product) {
            $product->delete();
        }

        $response = $this->actingAs($this->admin)->getJson('/api/products/trashed?per_page=5');

        $response->assertOk();
        $response->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            'message',
            'statusCode'
        ]);

        $json = $response->json();

        expect($json['data'])->toHaveCount(5);
        expect($json['meta']['total'])->toBe(10);
        expect($json['message'])->toBe("Product retrieved successfully");
        expect($json['statusCode'])->toBe(200);
    });
    it("returns empty data when no products are soft deleted", function () {
        $response = $this->actingAs($this->admin)->getJson('/api/products/trashed');

        $response->assertOk();
        $response->assertJson([
            'data' => [],
            'message' => 'Product retrieved successfully',
            'statusCode' => 200
        ]);
    });
    it("can paginate trashed products", function () {

        \App\Models\Product::limit(15)->get()->each->delete();

        $response = $this->actingAs($this->admin)->getJson('/api/products/trashed?per_page=10&page=2');

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(5);
        expect($response->json('meta.current_page'))->toBe(2);
    });
    it("only returns soft deleted products", function () {
        \App\Models\Product::limit(5)->get()->each->delete();

        $response = $this->actingAs($this->admin)->getJson('/api/products/trashed');

        $response->assertOk();

        collect($response->json('data'))->each(function ($product) {
            $productInDb = \App\Models\Product::withTrashed()->find($product['id']);
            expect($productInDb->trashed())->toBeTrue();
        });

    });

});
