<?php

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Default User',
            'email' => 'example@example.com',
            'password' => bcrypt('abc123')
        ]);

        Account::create([
            'name' => 'Bank',
            'type' => Account::TYPE_ASSET
        ]);

        Account::create([
            'name' => 'Stock',
            'type' => Account::TYPE_ASSET
        ]);

        Account::create([
            'name' => 'Cost of Goods Sold',
            'type' => Account::TYPE_EXPENSE
        ]);

        Account::create([
            'name' => 'Sales',
            'type' => Account::TYPE_INCOME
        ]);

        Account::create([
            'name' => 'GST',
            'type' => Account::TYPE_LIABILITY
        ]);
    }
}
