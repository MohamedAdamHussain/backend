<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\Permission;
use Modules\Auth\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء الأدوار
        $roles = ['admin', 'manager', 'employee'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. إنشاء الصلاحيات مرتبة حسب الـ module
        $permissions = [
            // Auth
            'auth' => [
                'view-users', 'create-users', 'edit-users', 'delete-users',
                'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
                'view-permissions', 'create-permissions', 'edit-permissions', 'delete-permissions',
                'assign-roles', 'remove-roles', 'assign-permissions', 'remove-permissions',
            ],
            // Inventory
            'inventory' => [
                'view-products', 'create-products', 'edit-products', 'delete-products',
                'view-warehouses', 'create-warehouses', 'edit-warehouses', 'delete-warehouses',
                'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
                'view-supply-orders', 'create-supply-orders', 'complete-supply-orders', 'cancel-supply-orders',
                'view-stock-transfers', 'create-stock-transfers', 'complete-stock-transfers', 'cancel-stock-transfers',
                'view-inventory-counts', 'create-inventory-counts', 'complete-inventory-counts', 'cancel-inventory-counts',
            ],
            // Sales
            'sales' => [
                'view-customers', 'create-customers', 'edit-customers', 'delete-customers',
                'view-price-lists', 'create-price-lists', 'edit-price-lists', 'delete-price-lists',
                'view-sale-orders', 'create-sale-orders', 'complete-sale-orders', 'cancel-sale-orders','accept-sale-orders','process-sale-orders','ship-sale-orders','complete-sale-orders','cancel-sale-orders',
                'view-invoices', 'create-invoices', 'pay-invoices', 'cancel-invoices',
                'view-delivery-orders', 'create-delivery-orders', 'complete-delivery-orders', 'cancel-delivery-orders',
            ],
            // HR
            'hr' => [
                'view-employees', 'create-employees', 'edit-employees', 'delete-employees',
                'view-departments', 'create-departments', 'edit-departments', 'delete-departments',
                'view-attendances', 'create-attendances', 'edit-attendances', 'delete-attendances',
                'view-absences', 'create-absences', 'edit-absences', 'delete-absences',
                'view-advances', 'create-advances', 'edit-advances', 'delete-advances',
                'view-salaries', 'create-salaries', 'mark-salaries-paid', 'mark-salaries-unpaid',
            ],
            // Accounting
            'accounting' => [
                'view-accounts', 'create-accounts', 'edit-accounts', 'delete-accounts',
                'view-journal-entries',
            ],
        ];

        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                Permission::firstOrCreate([
                    'name'   => $permission,
                    'module' => $module,
                ]);
            }
        }

        // 3. تعيين كل الصلاحيات للـ admin
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->permissions()->sync(Permission::pluck('id'));
    }
}
