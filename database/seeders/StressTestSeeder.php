<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Tenant;

class StressTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Must be run within a tenant context
        if (!Tenant::checkCurrent()) {
            $this->command->error('No tenant context. Please run this inside a tenant loop.');
            return;
        }

        $faker = Faker::create('ar_SA');
        $tenantId = Tenant::current()->id;

        $this->command->info("Starting Stress Test Data Seeding for Tenant ID: {$tenantId}...");

        // 1. Seed 1000 Customers
        $this->command->info('Seeding 1000 Customers...');
        $customers = [];
        $parentCustomer = DB::table('acc_head')->where('code', '122')->first();
        $parentId = $parentCustomer ? $parentCustomer->id : 0;
        
        // Start code from 1220000 to avoid duplicates
        $lastCustomer = DB::table('acc_head')->where('parent_id', $parentId)->orderBy('code', 'desc')->first();
        $startCode = $lastCustomer ? (intval($lastCustomer->code) + 1) : 1220001;

        for ($i = 0; $i < 1000; $i++) {
            $customers[] = [
                'code' => (string)($startCode + $i),
                'aname' => 'عميل تجريبي ' . $i . ' ' . uniqid(),
                'parent_id' => $parentId,
                'phone' => substr($faker->phoneNumber, 0, 20),
                'address' => $faker->address,
                'is_basic' => 0,
                'isdeleted' => 0,
                'tenant' => $tenantId,
                'mdtime' => now(),
                'crtime' => now(),
            ];
        }
        foreach (array_chunk($customers, 200) as $chunk) {
            DB::table('acc_head')->insert($chunk);
        }
        
        $customerIds = DB::table('acc_head')->where('parent_id', $parentId)->pluck('id')->toArray();

        // 2. Seed 5000 Items
        $this->command->info('Seeding 5000 Items...');
        $lastItem = DB::table('myitems')->orderBy('code', 'desc')->first();
        $timePrefix = rand(20000000, 30000000) * 10;
        $items = [];
        for ($i = 0; $i < 5000; $i++) {
            $items[] = [
                'iname' => 'صنف تجريبي ' . $timePrefix . '_' . $i,
                'code' => $timePrefix + $i,
                'barcode' => 'bc_' . time() . '_' . $i,
                'itmqty' => rand(10, 1000),
                'cost_price' => rand(10, 100),
                'price1' => rand(110, 200),
                'group1' => 1,
                'isdeleted' => 0,
                'tenant' => $tenantId,
                'mdtime' => now(),
                'crtime' => now(),
            ];
        }
        foreach (array_chunk($items, 500) as $chunk) {
            DB::table('myitems')->insert($chunk);
        }

        $itemIds = DB::table('myitems')->pluck('id')->toArray();

        // 3. Seed 2000 Invoices (B2B)
        $this->command->info('Seeding 2000 Invoices...');
        $invoices = [];
        $invPrefix = substr(uniqid(), 8);
        for ($i = 0; $i < 2000; $i++) {
            $proNum = 'INV-' . $invPrefix . '-' . str_pad($i, 5, '0', STR_PAD_LEFT);
            $invoices[] = [
                'pro_tybe' => 3, // TYPE_SALE
                'store_id' => 1,
                'acc1' => $faker->randomElement($customerIds),
                'pro_date' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                'pro_num' => $proNum,
                'order_type' => 'takeaway',
                'fat_total' => rand(100, 5000),
                'fat_net' => rand(100, 5000),
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'invoice_status' => 'completed',
                'tenant' => $tenantId,
                'crtime' => now(),
                'mdtime' => now(),
            ];
        }
        foreach (array_chunk($invoices, 500) as $chunk) {
            DB::table('ot_head')->insert($chunk);
        }

        $this->command->info("Stress test data seeded successfully!");
    }
}
