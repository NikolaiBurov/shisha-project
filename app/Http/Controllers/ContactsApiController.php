<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Requests\ContactPostRequest;

class ContactsApiController extends Controller
{
    /**
     * Store a new blog post.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ContactPostRequest $contactRequest)
    {
        //todo Make a response class
        try {
            $contact = Contact::create($contactRequest->onlyInRules());

            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => $contact,
                'error_message' => null
            ];
        } catch (\Exception $e) {
            $response = [
                'status_code' => array_keys(get_object_vars($this->status_codes->postRequests()))[0],
                'data' => null,
                'error_message' => $e->getMessage()
            ];
        }

        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Contact $Contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Contact $Contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $Contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Contact $Contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $Contact)
    {
        //
    }
}
