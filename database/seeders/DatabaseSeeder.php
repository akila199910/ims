<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            ExtraPermissionSeeder::class,
            PurchaseReturnPermissionSeeder::class,
            ReportPermissionSeeder::class,
            UserSeeder::class,
            PayementTypeSeeder::class,
            PayementTermsSeeder::class
        ]);
    }
}
