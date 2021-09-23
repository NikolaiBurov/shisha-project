<?php

namespace Database\Seeders;

use App\Models\PublicUser;
use Illuminate\Database\Seeder;

class PublicUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (PublicUser::count() == 0) {
            PublicUser::create([
                'username' => 'SvetlinNikolov',
                'email' => 'daxtera@email.com',
                'password' => '1234',
                'city' => 'Sofia',
                'address' => 'zonata'
            ]);

            PublicUser::create([
                'username' => 'NikolaiBurov',
                'email' => 'santa@email.com',
                'password' => '1234',
                'city' => 'Sofia',
                'address' => 'zonata'
            ]);
        }
    }
}
