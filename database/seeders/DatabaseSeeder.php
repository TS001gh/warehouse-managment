<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Group;
use App\Models\Inbound;
use App\Models\Item;
use App\Models\Outbound;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => "Admin@123"
        ]);

        // Create 10 groups
        Group::factory(10)->create();

        // Create 50 items associated with groups
        Item::factory(50)->create();

        // Create 20 customers and 20 suppliers to be reused in factories
        Customer::factory(20)->create();
        Supplier::factory(20)->create();

        // Create 100 inbound records with existing suppliers
        Inbound::factory(100)->create();

        // Create 100 outbound records with existing customers
        Outbound::factory(100)->create();
    }
}
