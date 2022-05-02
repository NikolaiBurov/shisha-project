<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
         return [
            'title' =>  $this->faker->name(),
            'description' => $this->faker->text(),
            'email' => $this->faker->unique()->safeEmail(),
            'created_at' => now(),
            'updated_at' =>  now(),
        ];
    }
}
