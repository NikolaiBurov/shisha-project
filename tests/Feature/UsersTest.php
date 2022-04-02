<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UsersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_all_users_endpoint()
    {
        $headers = [
            'HTTP_Accept' => 'application/json',
            'HTTP_jwt_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJDSEFOR0VNRSIsImF1ZCI6IkNIQU5HRU1FIn0.SlhcxUKU0Br4-X02m4h7AEVPhSrgaWe9tRFhrT7q_hc'
        ];

        $response = $this->withHeaders($headers)->get('/api/users/get-all-users');

        $this->assertEquals(200, $response->baseResponse->original['status_code']);
    }
}
