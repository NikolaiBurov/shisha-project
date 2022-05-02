<?php

namespace Tests\Unit;

use Database\Factories\ContactFactory;
use Tests\TestCase;
use App\Models\Contact;

class ContactsTest extends TestCase
{
    private array $headers = [
        'HTTP_Accept' => 'application/json',
        'HTTP_jwt_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJDSEFOR0VNRSIsImF1ZCI6IkNIQU5HRU1FIn0.SlhcxUKU0Br4-X02m4h7AEVPhSrgaWe9tRFhrT7q_hc'
    ];


    public function testAddingRecordContactApi()
    {
        //Preparing data
        $data = $this->prepareData();

        //Calling api
        $response = $this->withHeaders($this->headers)->post('/api/contact-us/add', $data);

        //Asserting response
        $this->assertEquals(200, $response->baseResponse->original['status_code']);
    }

    public function testContactCreateResponseIsObject()
    {
        //Preparing data
        $data = $this->prepareData();

        //Calling api
        $response = $this->withHeaders($this->headers)->post('/api/contact-us/add', $data);

        //Asserting response
        $this->assertIsObject($response->original['data']);
    }

    public function testContactHasAttributesInResponse()
    {
        //Preparing data
        $data = $this->prepareData();

        //Calling api
        $response = $this->withHeaders($this->headers)->post('/api/contact-us/add', $data);

        //Asserting response
       $this->assertObjectHasAttribute('title', $response->baseResponse->getData()->data);
       $this->assertObjectHasAttribute('description', $response->baseResponse->getData()->data);
       $this->assertObjectHasAttribute('email',$response->baseResponse->getData()->data);
    }


    private function prepareData()
    {
        return Contact::factory()->create()->toArray();
    }
}
