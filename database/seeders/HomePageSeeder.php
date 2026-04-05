<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomePage; // อย่าลืม import model
use Illuminate\Support\Facades\DB;

class HomePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่ เพื่อป้องกันการสร้างซ้ำ
        if (HomePage::count() == 0) {
            HomePage::create([
                'content' => json_encode(['main_html' => '<p>Welcome to your website! Please edit this content via the admin panel.</p>']),
                'meta_title' => 'Initial Home Page',
                'meta_description' => 'The default description for the main page.',
                'meta_keywords' => 'home, initial, default',
                'active' => true,
                'revision' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Initial Home Page content created.');
        } else {
            $this->command->info('Home Page content already exists. Skipping seeder.');
        }
    }
}
