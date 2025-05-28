<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            FeatureSeeder::class,
            OperationSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
            PermissionPositionSeeder::class,
            OutletSeeder::class,
            EmployeeSeeder::class,
            BaseUnitSeeder::class,
            UnitSeeder::class,
            DepartmentSeeder::class,
            CategorySeeder::class,
            //ProductSeeder::class,
        ]);
    }
}
