<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Product;
use Modules\shared\Http\Traits\ApiResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use ApiResponse;

    public function stats()
    {
        $now = Carbon::now()->toDateString();
        $thirtyDaysLater = Carbon::now()->addDays(30)->toDateString();

        // إجمالي المنتجات
        $totalProducts = Product::count();

        // إجمالي قيمة المخزون — price * quantity من كل المستودعات
        $inventoryValue = DB::table('products')
            ->join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
            ->sum(DB::raw('products.price * product_warehouse.quantity'));

        // إجمالي الكميات
        $totalQuantity = DB::table('product_warehouse')->sum('quantity');

        // المنتجات منتهية الصلاحية
        $expiredProducts = Product::whereNotNull('expiry_date')
            ->where('expiry_date', '<', $now)
            ->count();

        // المنتجات التي توشك على الانتهاء خلال 30 يوم
        $expiringSoon = Product::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$now, $thirtyDaysLater])
            ->count();

        // المنتجات منخفضة المخزون
        $lowStock = DB::table('product_warehouse')
            ->whereRaw('quantity <= low_stock_alert')
            ->where('quantity', '>', 0)
            ->count();

        return $this->successResponse([
            'total_products'   => $totalProducts,
            'inventory_value'  => round($inventoryValue, 2),
            'total_quantity'   => $totalQuantity,
            'expired_products' => $expiredProducts,
            'expiring_soon'    => $expiringSoon,
            'low_stock'        => $lowStock,
        ]);
    }
}
