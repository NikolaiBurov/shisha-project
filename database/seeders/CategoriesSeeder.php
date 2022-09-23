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
                'id' => 1,
                'name' => 'ADALIYA',
                'slug' => 'ADALIYA',
            ]);

            Category::create([
                'id' => 2,
                'name' => 'HOOKAIN',
                'slug' => 'HOOKAIN',
            ]);
            Category::create([
                'id' => 3,
                'name' => 'OZ',
                'slug' => 'OZ',
            ]);
            Category::create([
                'id' => 4,
                'name' => 'DARKSIDE',
                'slug' => 'DARKSIDE',
            ]);
        }
    }
}
