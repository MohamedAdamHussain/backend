<?php
namespace Modules\Inventory\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockTransfer;
use Modules\Inventory\Models\Warehouse;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// ✅ Test 1 — transit ناجح
it('deducts stock from source warehouse when transited', function () {
    // Arrange
    $fromWarehouse = Warehouse::factory()->create();
    $toWarehouse   = Warehouse::factory()->create();
    $product       = Product::factory()->create();

    // مخزون ابتدائي = 100
    $fromWarehouse->products()->attach($product, [
        'quantity'        => 100,
        'low_stock_alert' => 10,
    ]);

    $transfer = StockTransfer::factory()->create([
        'from_warehouse_id' => $fromWarehouse->id,
        'to_warehouse_id'   => $toWarehouse->id,
        'status'            => 'pending',
    ]);

    $transfer->items()->create([
        'product_id'  => $product->id,
        'quantity'    => 30,
        'unit_price'  => 10,
        'total_price' => 300,
    ]);

    // Act
    $response = $this->postJson("/api/stock-transfers/{$transfer->id}/transit");

    // Assert
    $response->assertStatus(200);

    $this->assertDatabaseHas('product_warehouse', [
        'warehouse_id' => $fromWarehouse->id,
        'product_id'   => $product->id,
        'quantity'     => 70, // 100 - 30
    ]);
});

// ❌ Test 2 — مخزون غير كافٍ
it('fails transit when stock is insufficient', function () {
    $fromWarehouse = Warehouse::factory()->create();
    $toWarehouse   = Warehouse::factory()->create();
    $product       = Product::factory()->create();

    // مخزون = 5 فقط
    $fromWarehouse->products()->attach($product, [
        'quantity'        => 5,
        'low_stock_alert' => 10,
    ]);

    $transfer = StockTransfer::factory()->create([
        'from_warehouse_id' => $fromWarehouse->id,
        'to_warehouse_id'   => $toWarehouse->id,
        'status'            => 'pending',
    ]);

    $transfer->items()->create([
        'product_id'  => $product->id,
        'quantity'    => 10, // أكثر من المتاح
        'unit_price'  => 10,
        'total_price' => 100,
    ]);

    // Act
    $response = $this->postJson("/api/stock-transfers/{$transfer->id}/transit");

    // Assert
    $response->assertStatus(422);

    // المخزون لم يتغير
    $this->assertDatabaseHas('product_warehouse', [
        'warehouse_id' => $fromWarehouse->id,
        'product_id'   => $product->id,
        'quantity'     => 5,
    ]);
});
