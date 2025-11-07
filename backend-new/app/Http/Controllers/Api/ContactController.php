<?php

namespace App\Http\Controllers\Api;

use App\Services\ContactService;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;

class ContactController extends BaseApiController
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index()
    {
        $contact = $this->contactService->getContactInfo();

        // Check if contact exists
        if ($contact) {
            // Use single resource, not collection, since getFirst() returns a single object
            return $this->success(new ContactResource($contact), 'Contact information retrieved successfully');
        }

        // Return empty data if no contact found
        return $this->success(null, 'No contact information available');
    }
}
