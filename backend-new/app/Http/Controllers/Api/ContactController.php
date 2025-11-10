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
            Log::info('Fetching contact information');
            $contact = $this->contactService->getContactInfo();

            // Check if contact exists
            if ($contact) {
                Log::info('Contact found', ['contact_id' => $contact->id]);
                // Use single resource, not collection, since getFirst() returns a single object
                return $this->success(new ContactResource($contact), 'Contact information retrieved successfully');
            }

            Log::info('No contact found');
            // Return empty data if no contact found
            return $this->success(null, 'No contact information available');
        } catch (\Exception $e) {
            Log::error('Error fetching contact information', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->error('Failed to retrieve contact information', 500);
        }
    }
}
