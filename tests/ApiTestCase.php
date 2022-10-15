<?php

namespace Tests;

use Tests\TestCase;

class ApiTestCase extends TestCase
{
    /**
     * @var array|string[]
     */
    protected array $headers = [
        'HTTP_Accept' => 'application/json',
        'HTTP_jwt_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJDSEFOR0VNRSIsImF1ZCI6IkNIQU5HRU1FIn0.SlhcxUKU0Br4-X02m4h7AEVPhSrgaWe9tRFhrT7q_hc'
    ];

    /**
     * @var string
     */
    protected const USERS_ENDPOINT = '/api/users';
}
