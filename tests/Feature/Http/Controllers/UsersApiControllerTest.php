<?php

namespace Tests\Http\Controllers;

use App\Models\PublicUser;
use Database\Factories\PublicUserFactory;
use Faker\Provider\Address;
use Illuminate\Http\Response;
use Tests\ApiTestCase;
use Faker\Generator;

class UsersApiControllerTest extends ApiTestCase
{
    /**
     * @return void
     */
    public function test_if_get_user_by_username_returns_incorrect_data(): void
    {
        $endpoint = config('app.url') . self::USERS_ENDPOINT . '/get-user-by-email';

        $user = PublicUserFactory::new()
            ->create();

        $response = $this->withHeaders($this->headers)->post($endpoint, ['email' => $user->email]);

        $response->assertJson([
            'status_code' => Response::HTTP_OK,
            'error_message' => 'The selected email is invalid.|',
        ]);
    }
}
