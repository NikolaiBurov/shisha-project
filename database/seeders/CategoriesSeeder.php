<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run(): void
    {
        if (Category::count() == 0) {
            Category::create([
                'name' => 'ADALIYA',
                'slug' => 'ADALIYA',
            ]);

            Category::create([
                'name' => 'HOOKAIN',
                'slug' => 'HOOKAIN',
            ]);
            Category::create([
                'name' => 'OZ',
                'slug' => 'OZ',
            ]);
            Category::create([
                'name' => 'DARKSIDE',
                'slug' => 'DARKSIDE',
            ]);
        }
    }
}
