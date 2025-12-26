<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Simple Text List
        $categories = [
            '🕋 General',
            '🌙 Ramadan',
            '🕋 Hajj & Umrah',
            '🏥 Health',
            '💼 Rizq & Work',
            '❤️ Marriage',
            '👨‍👩‍👧 Family',
            '🌧 Hardship',
            '🧠 Guidance',
            '🤲 Gratitude',
        ];

        foreach ($categories as $index => $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)], // Check slug to avoid duplicates
                [
                    'name' => $name, // <--- Saving as simple TEXT now
                    'is_active' => true,
                    'order' => $index + 1,
                ]
            );
        }
    }
}