<?php

namespace Modules\Accounts\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounts\Models\Account;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    protected $accounts = [
        // أصول
        ['name' => 'المخزون',        'slug' => 'inventory',       'type' => 'asset'],
        ['name' => 'النقدية',        'slug' => 'cash',            'type' => 'asset'],
        ['name' => 'البنك',          'slug' => 'bank',            'type' => 'asset'],
        ['name' => 'حسابات القبض',   'slug' => 'receivables',     'type' => 'asset'],

        // خصوم
        ['name' => 'حسابات الدفع',   'slug' => 'payables',        'type' => 'liability'],
        ['name' => 'الموردون',        'slug' => 'suppliers',       'type' => 'liability'],

        // إيرادات
        ['name' => 'إيرادات المبيعات', 'slug' => 'sales-revenue',  'type' => 'revenue'],

        // مصاريف
        ['name' => 'مصاريف الرواتب', 'slug' => 'salaries-expense', 'type' => 'expense'],
        ['name' => 'تكلفة البضاعة',  'slug' => 'cogs',            'type' => 'expense'],
    ];
    public function run(): void
    {
        foreach ($this->accounts as $account) {
            Account::firstOrCreate(
                ['slug' => $account['slug']],
                [
                    'name' => $account['name'],
                    'type' => $account['type'],
                ]
            );
        }
        // $this->call([]);
    }
}
