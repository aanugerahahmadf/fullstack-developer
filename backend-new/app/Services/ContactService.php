<?php

namespace App\Services;

use App\Repositories\ContactRepository;
use Illuminate\Support\Facades\Cache;

class ContactService extends BaseService
{
    protected $contactRepository;

    public function __construct(ContactRepository $contactRepository)
    {
        parent::__construct($contactRepository);
        $this->contactRepository = $contactRepository;
    }

    public function getContactInfo()
    {
        // Ultra-fast cache with 0.5 second TTL for maximum responsiveness
        return Cache::remember('contact_info', 0.5, function () {
            return $this->contactRepository->getFirst();
        });
    }
}
