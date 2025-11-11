<?php

namespace App\Http\Controllers\Api;

use App\Services\ContactService;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends BaseApiController
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    public function index()
    {
        try {
            // Ultra-fast response with minimal logging
            $contact = $this->contactService->getContactInfo();

            // Check if contact exists
            if ($contact) {
                // Use single resource, not collection, since getFirst() returns a single object
                return $this->success(new ContactResource($contact), 'Contact information retrieved successfully');
            }

            // Return empty data if no contact found
            return $this->success(null, 'No contact information available');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve contact information', 500);
        }
    }
}
