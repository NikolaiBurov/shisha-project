<?php

namespace Database\Seeders;

use App\Models\Flavour;
use Illuminate\Database\Seeder;

class FlavourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public static function run()
    {
        if (Flavour::count() == 0) {
            Flavour::create([
                'title' => 'black nigger',
                'description' => 'test',
                'short_description' => "test",
                'image' => "storage/app/public/statisPictures/image_product_placeholder.svg",
                'category_id' => 1,
                'in_stock' => true,
                'flavour_type' => 'sorrow'
            ]);

            Flavour::create([
                'title' => 'black nigger2',
                'description' => 'test',
                'short_description' => "test",
                'image' => "storage/app/public/statisPictures/image_product_placeholder.svg",
                'category_id' => 2,
                'in_stock' => true,
                'flavour_type' => 'sorrow'
            ]);
            Flavour::create([
                'title' => 'black nigger3',
                'description' => 'test',
                'short_description' => "test",
                'image' => "storage/app/public/statisPictures/image_product_placeholder.svg",
                'category_id' => 3,
                'in_stock' => true,
                'flavour_type' => 'sorrow'
            ]);
            Flavour::create([
                'title' => 'black nigger4',
                'description' => 'test',
                'short_description' => "test",
                'image' => "storage/app/public/statisPictures/image_product_placeholder.svg",
                'category_id' => 4,
                'in_stock' => true,
                'flavour_type' => 'sorrow'
            ]);
        }
    }
}
